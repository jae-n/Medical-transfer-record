<?php
require 'config.php';

$errors = [];
$success = false;
$updatedAppointment = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appID   = filter_input(INPUT_POST, 'appID', FILTER_VALIDATE_INT);
    $appDate = $_POST['appDate'] ?? '';
    $rawPid  = $_POST['pid']   ?? '';
    $rawDoc  = $_POST['docID'] ?? '';
    $status  = trim($_POST['status'] ?? '');

    $validStatuses = ['Requested', 'Approved', 'Cancelled'];

    // appID is always required
    if ($appID === false || $appID <= 0) {
        $errors[] = 'Invalid appointment ID.';
    }

    // Optional: pid and docID – only validate if provided
    $pid = null;
    if ($rawPid !== '') {
        $pid = filter_var($rawPid, FILTER_VALIDATE_INT);
        if ($pid === false || $pid <= 0) {
            $errors[] = 'Invalid patient ID.';
        }
    }

    $docID = null;
    if ($rawDoc !== '') {
        $docID = filter_var($rawDoc, FILTER_VALIDATE_INT);
        if ($docID === false || $docID <= 0) {
            $errors[] = 'Invalid doctor ID.';
        }
    }

    // status is optional but must be valid if provided
    if ($status !== '' && !in_array($status, $validStatuses, true)) {
        $errors[] = 'Invalid status selected.';
    }

    // Build dynamic SET clause
    $setClauses = [];
    $params = [':appID' => $appID];

    if ($appDate !== '') {
        $setClauses[] = 'appDate = :appDate';
        $params[':appDate'] = $appDate;
    }

    if ($pid !== null) {
        $setClauses[] = 'pid = :pid';
        $params[':pid'] = $pid;
    }

    if ($docID !== null) {
        $setClauses[] = 'docID = :docID';
        $params[':docID'] = $docID;
    }

    if ($status !== '') {
        $setClauses[] = 'status = :status';
        $params[':status'] = $status;
    }

    if (empty($setClauses) && empty($errors)) {
        $errors[] = 'No fields provided to update. Please fill in at least one field.';
    }

    if (empty($errors)) {
        try {
            // Check appointment exists
            $checkStmt = $pdo->prepare(
                "SELECT appID FROM Appointment WHERE appID = :appID"
            );
            $checkStmt->execute([':appID' => $appID]);
            $exists = $checkStmt->fetch();

            if (!$exists) {
                $errors[] = "No appointment found with ID $appID.";
            } else {
                // Perform dynamic UPDATE
                $sql = "UPDATE Appointment
                        SET " . implode(', ', $setClauses) . "
                        WHERE appID = :appID";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Fetch updated row to display
                $fetchStmt = $pdo->prepare(
                    "SELECT appID, appDate, pid, docID, status
                     FROM Appointment
                     WHERE appID = :appID"
                );
                $fetchStmt->execute([':appID' => $appID]);
                $updatedAppointment = $fetchStmt->fetch(PDO::FETCH_ASSOC);

                if ($updatedAppointment) {
                    $success = true;
                } else {
                    $errors[] = 'Appointment was updated but could not be re-fetched.';
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
  <title>Update Appointment</title>
</head>
<body>
<h1>Update Appointment</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $updatedAppointment): ?>
  <p>Appointment successfully updated:</p>
  <ul>
    <li><strong>ID:</strong> <?= sanitize_output($updatedAppointment['appID']) ?></li>
    <li><strong>Date:</strong> <?= sanitize_output($updatedAppointment['appDate']) ?></li>
    <li><strong>Patient ID:</strong> <?= sanitize_output($updatedAppointment['pid']) ?></li>
    <li><strong>Doctor ID:</strong> <?= sanitize_output($updatedAppointment['docID']) ?></li>
    <li><strong>Status:</strong> <?= sanitize_output($updatedAppointment['status']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to update an appointment.</p>
<?php endif; ?>

<p><a href="../public/appointment/update_appointment.html">Back to Update Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
