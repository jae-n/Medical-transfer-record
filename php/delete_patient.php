<?php
require 'config.php';

$errors  = [];
$success = false;
$deletedPatient = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);

    if ($pid === false || $pid <= 0) {
        $errors[] = 'Invalid patient ID.';
    }

    if (empty($errors)) {
        try {
            // Check if patient exists (optional but helpful)
            $checkStmt = $pdo->prepare(
                "SELECT pid, name FROM Patient WHERE pid = :pid"
            );
            $checkStmt->execute([':pid' => $pid]);
            $patient = $checkStmt->fetch();

            if (!$patient) {
                $errors[] = "No patient found with ID $pid.";
            } else {
                // Delete patient
                $deleteStmt = $pdo->prepare(
                    "DELETE FROM Patient WHERE pid = :pid"
                );
                $deleteStmt->execute([':pid' => $pid]);

                if ($deleteStmt->rowCount() > 0) {
                    $success = true;
                    $deletedPatient = $patient;
                } else {
                    $errors[] = "Delete failed for patient ID $pid.";
                }
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
  <title>Delete Patient</title>
</head>
<body>
<h1>Delete Patient</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $deletedPatient): ?>
  <p>Successfully deleted patient:</p>
  <ul>
    <li><strong>ID:</strong> <?= sanitize_output($deletedPatient['pid']) ?></li>
    <li><strong>Name:</strong> <?= sanitize_output($deletedPatient['name']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to delete a patient.</p>
<?php endif; ?>

<p><a href="../public/patient/delete_patient.html">Back to Delete Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
