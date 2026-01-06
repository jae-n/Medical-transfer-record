<?php
require 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rid         = filter_input(INPUT_POST, 'rid', FILTER_VALIDATE_INT);
    $pid         = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
    $docID       = filter_input(INPUT_POST, 'docID', FILTER_VALIDATE_INT);
    $diagnosis   = trim($_POST['diagnosis'] ?? '');
    $treatment   = trim($_POST['treatment'] ?? '');
    $hospitalName = trim($_POST['hospitalName'] ?? '');

    if ($rid === false || $rid <= 0) {
        $errors[] = 'Invalid record ID.';
    }
    if ($pid === false || $pid <= 0) {
        $errors[] = 'Invalid patient ID.';
    }
    if ($docID === false || $docID <= 0) {
        $errors[] = 'Invalid doctor ID.';
    }
    if ($diagnosis === '') {
        $errors[] = 'Diagnosis is required.';
    }
    if ($treatment === '') {
        $errors[] = 'Treatment is required.';
    }
    if ($hospitalName === '') {
        $errors[] = 'Hospital name is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO Record (rid, pid, docID, diagnosis, treatment, hospitalName)
                 VALUES (:rid, :pid, :docID, :diagnosis, :treatment, :hospitalName)"
            );
            $stmt->execute([
                ':rid'          => $rid,
                ':pid'          => $pid,
                ':docID'        => $docID,
                ':diagnosis'    => $diagnosis,
                ':treatment'    => $treatment,
                ':hospitalName' => $hospitalName,
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
<head><meta charset="UTF-8"><title>Add Medical Record</title></head>
<body>
<h1>Add Medical Record</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success): ?>
  <p>Record successfully added.</p>
<?php endif; ?>

<p><a href="../public/record/add_record.html">Back to Record Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
