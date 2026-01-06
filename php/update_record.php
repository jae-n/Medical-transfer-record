<?php
require 'config.php';

$errors = [];
$success = false;
$updatedRecord = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rid          = filter_input(INPUT_POST, 'rid', FILTER_VALIDATE_INT);
    $pid          = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
    $docID        = filter_input(INPUT_POST, 'docID', FILTER_VALIDATE_INT);
    $diagnosis    = trim($_POST['diagnosis'] ?? '');
    $treatment    = trim($_POST['treatment'] ?? '');
    $hospitalName = trim($_POST['hospitalName'] ?? '');

    if ($rid === false || $rid <= 0) {
        $errors[] = 'Invalid record ID.';
    }

    if (empty($errors)) {
        try {
            // Check if record exists
            $checkStmt = $pdo->prepare(
                "SELECT rid FROM Record WHERE rid = :rid"
            );
            $checkStmt->execute([':rid' => $rid]);
            $exists = $checkStmt->fetch();

            if (!$exists) {
                $errors[] = "No record found with ID $rid.";
            } else {
                // Build dynamic update query based on provided fields
                $updateFields = [];
                $params = [':rid' => $rid];

                if ($pid !== null && $pid > 0) {
                    $updateFields[] = "pid = :pid";
                    $params[':pid'] = $pid;
                }

                if ($docID !== null && $docID > 0) {
                    $updateFields[] = "docID = :docID";
                    $params[':docID'] = $docID;
                }

                if ($diagnosis !== '') {
                    $updateFields[] = "diagnosis = :diagnosis";
                    $params[':diagnosis'] = $diagnosis;
                }

                if ($treatment !== '') {
                    $updateFields[] = "treatment = :treatment";
                    $params[':treatment'] = $treatment;
                }

                if ($hospitalName !== '') {
                    $updateFields[] = "hospitalName = :hospitalName";
                    $params[':hospitalName'] = $hospitalName;
                }

                if (empty($updateFields)) {
                    $errors[] = 'No fields provided for update.';
                } else {
                    $sql = "UPDATE Record SET " . implode(', ', $updateFields) . " WHERE rid = :rid";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                    $success = true;
                    $updatedRecord = [
                        'rid'          => $rid,
                        'pid'          => $pid,
                        'docID'        => $docID,
                        'diagnosis'    => $diagnosis,
                        'treatment'    => $treatment,
                        'hospitalName' => $hospitalName,
                    ];
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
  <title>Update Medical Record</title>
</head>
<body>
  <h1>Update Medical Record</h1>

  <?php if (!empty($errors)): ?>
    <h2>Errors</h2>
    <ul>
      <?php foreach ($errors as $err): ?>
        <li><?= sanitize_output($err) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php elseif ($success && $updatedRecord): ?>
    <p>Record successfully updated:</p>
    <ul>
      <li><strong>Record ID:</strong> <?= sanitize_output($updatedRecord['rid']) ?></li>
      <?php if ($updatedRecord['pid'] > 0): ?>
        <li><strong>Patient ID:</strong> <?= sanitize_output($updatedRecord['pid']) ?></li>
      <?php endif; ?>
      <?php if ($updatedRecord['docID'] > 0): ?>
        <li><strong>Doctor ID:</strong> <?= sanitize_output($updatedRecord['docID']) ?></li>
      <?php endif; ?>
      <?php if ($updatedRecord['diagnosis'] !== ''): ?>
        <li><strong>Diagnosis:</strong> <?= sanitize_output($updatedRecord['diagnosis']) ?></li>
      <?php endif; ?>
      <?php if ($updatedRecord['treatment'] !== ''): ?>
        <li><strong>Treatment:</strong> <?= sanitize_output($updatedRecord['treatment']) ?></li>
      <?php endif; ?>
      <?php if ($updatedRecord['hospitalName'] !== ''): ?>
        <li><strong>Hospital Name:</strong> <?= sanitize_output($updatedRecord['hospitalName']) ?></li>
      <?php endif; ?>
    </ul>
  <?php else: ?>
    <p>Please submit the form to update a record.</p>
  <?php endif; ?>

  <p><a href="../public/record/update_record.html">Back to Update Form</a></p>
  <p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>