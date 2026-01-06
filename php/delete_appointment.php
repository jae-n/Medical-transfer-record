<?php
require 'config.php';

$errors = [];
$success = false;
$deletedAppointment = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appID = filter_input(INPUT_POST, 'appID', FILTER_VALIDATE_INT);

    if ($appID === false || $appID <= 0) {
        $errors[] = 'Invalid appointment ID.';
    }

    if (empty($errors)) {
        try {
            // Check if appointment exists
            $checkStmt = $pdo->prepare(
                "SELECT appID, appDate, pid, docID, status
                 FROM Appointment
                 WHERE appID = :appID"
            );
            $checkStmt->execute([':appID' => $appID]);
            $appointment = $checkStmt->fetch();

            if (!$appointment) {
                $errors[] = "No appointment found with ID $appID.";
            } else {
                // Delete appointment
                $deleteStmt = $pdo->prepare(
                    "DELETE FROM Appointment WHERE appID = :appID"
                );
                $deleteStmt->execute([':appID' => $appID]);

                if ($deleteStmt->rowCount() > 0) {
                    $success = true;
                    $deletedAppointment = $appointment;
                } else {
                    $errors[] = "Delete failed for appointment ID $appID.";
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
  <title>Delete Appointment</title>
</head>
<body>
<h1>Delete Appointment</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $deletedAppointment): ?>
  <p>Appointment successfully deleted:</p>
  <ul>
    <li><strong>Appointment ID:</strong> <?= sanitize_output($deletedAppointment['appID']) ?></li>
    <li><strong>Date:</strong> <?= sanitize_output($deletedAppointment['appDate']) ?></li>
    <li><strong>Patient ID:</strong> <?= sanitize_output($deletedAppointment['pid']) ?></li>
    <li><strong>Doctor ID:</strong> <?= sanitize_output($deletedAppointment['docID']) ?></li>
    <li><strong>Status:</strong> <?= sanitize_output($deletedAppointment['status']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to delete an appointment.</p>
<?php endif; ?>

<p><a href="../public/appointment/delete_appointment.html">Back to Delete Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
