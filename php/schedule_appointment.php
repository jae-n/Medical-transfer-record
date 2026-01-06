<?php
require 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appID   = filter_input(INPUT_POST, 'appID', FILTER_VALIDATE_INT);
    $appDate = $_POST['appDate'] ?? '';
    $pid     = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
    $docID   = filter_input(INPUT_POST, 'docID', FILTER_VALIDATE_INT);
    $status  = trim($_POST['status'] ?? '');

    $validStatuses = ['Requested','Approved','Cancelled'];

    if ($appID === false || $appID <= 0) {
        $errors[] = 'Invalid appointment ID.';
    }
    if ($appDate === '') {
        $errors[] = 'Appointment date is required.';
    }
    if ($pid === false || $pid <= 0) {
        $errors[] = 'Invalid patient ID.';
    }
    if ($docID === false || $docID <= 0) {
        $errors[] = 'Invalid doctor ID.';
    }
    if (!in_array($status, $validStatuses, true)) {
        $errors[] = 'Invalid status selected.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO Appointment (appID, appDate, pid, docID, status)
                 VALUES (:appID, :appDate, :pid, :docID, :status)"
            );
            $stmt->execute([
                ':appID'   => $appID,
                ':appDate' => $appDate,
                ':pid'     => $pid,
                ':docID'   => $docID,
                ':status'  => $status,
            ]);
            $success = true;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . sanitize_output($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Schedule Appointment</title></head>
<body>
<h1>Schedule Appointment</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success): ?>
  <p>Appointment successfully scheduled.</p>
<?php endif; ?>

<p><a href="../public/appointment/schedule_appointment.html">Back to Appointment Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
