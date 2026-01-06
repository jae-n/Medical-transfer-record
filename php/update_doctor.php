<?php
require 'config.php';

$errors = [];
$success = false;
$updatedDoctor = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docID          = filter_input(INPUT_POST, 'docID', FILTER_VALIDATE_INT);
    $specialization = trim($_POST['specialization'] ?? '');

    if ($docID === false || $docID <= 0) {
        $errors[] = 'Invalid doctor ID.';
    }
    if ($specialization === '') {
        $errors[] = 'Specialization is required.';
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
                $stmt = $pdo->prepare(
                    "UPDATE Doctor
                     SET specialization = :specialization
                     WHERE docID = :docID"
                );
                $stmt->execute([
                    ':docID'          => $docID,
                    ':specialization' => $specialization,
                ]);

                $success = true;
                $updatedDoctor = [
                    'docID'          => $docID,
                    'empID'          => $doctor['empID'],
                    'empName'        => $doctor['empName'],
                    'specialization' => $specialization,
                ];
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . sanitize_output($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Update Doctor</title>
</head>
<body>
<h1>Update Doctor</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $updatedDoctor): ?>
  <p>Doctor successfully updated:</p>
  <ul>
    <li><strong>Doctor ID:</strong> <?= sanitize_output($updatedDoctor['docID']) ?></li>
    <li><strong>Employee ID:</strong> <?= sanitize_output($updatedDoctor['empID']) ?></li>
    <li><strong>Name:</strong> <?= sanitize_output($updatedDoctor['empName'] ?? 'N/A') ?></li>
    <li><strong>Specialization:</strong> <?= sanitize_output($updatedDoctor['specialization']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to update a doctor.</p>
<?php endif; ?>

<p><a href="../public/doctor/update_doctor.html">Back to Update Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
