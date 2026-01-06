<?php
require 'config.php';

$errors = [];
$success = false;
$deletedDoctor = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docID = filter_input(INPUT_POST, 'docID', FILTER_VALIDATE_INT);

    if ($docID === false || $docID <= 0) {
        $errors[] = 'Invalid doctor ID.';
    }

    if (empty($errors)) {
        try {
            // Check if doctor exists
            $checkStmt = $pdo->prepare(
                "SELECT d.docID, d.empID, d.specialization, e.name AS empName
                 FROM Doctor d
                 LEFT JOIN Employee e ON d.empID = e.empID
                 WHERE d.docID = :docID"
            );
            $checkStmt->execute([':docID' => $docID]);
            $doctor = $checkStmt->fetch();

            if (!$doctor) {
                $errors[] = "No doctor found with ID $docID.";
            } else {
                // Delete doctor
                $deleteStmt = $pdo->prepare(
                    "DELETE FROM Doctor WHERE docID = :docID"
                );
                $deleteStmt->execute([':docID' => $docID]);

                if ($deleteStmt->rowCount() > 0) {
                    $success = true;
                    $deletedDoctor = $doctor;
                } else {
                    $errors[] = "Delete failed for doctor ID $docID.";
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Cannot delete this doctor because they are referenced in other tables.';
            } else {
                $errors[] = 'Database error: ' . sanitize_output($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Delete Doctor</title>
</head>
<body>
<h1>Delete Doctor</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $deletedDoctor): ?>
  <p>Doctor successfully deleted:</p>
  <ul>
    <li><strong>Doctor ID:</strong> <?= sanitize_output($deletedDoctor['docID']) ?></li>
    <li><strong>Employee ID:</strong> <?= sanitize_output($deletedDoctor['empID']) ?></li>
    <li><strong>Name:</strong> <?= sanitize_output($deletedDoctor['empName'] ?? 'N/A') ?></li>
    <li><strong>Specialization:</strong> <?= sanitize_output($deletedDoctor['specialization']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to delete a doctor.</p>
<?php endif; ?>

<p><a href="../public/doctor/delete_doctor.html">Back to Delete Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
