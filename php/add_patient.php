<?php
require 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid    = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
    $name   = trim($_POST['name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');

    if ($pid === false || $pid <= 0) {
        $errors[] = 'Invalid patient ID.';
    }
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($gender === '') {
        $errors[] = 'Gender is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if ($phone === '') {
        $errors[] = 'Phone is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO Patient (pid, name, gender, email, phone)
                 VALUES (:pid, :name, :gender, :email, :phone)"
            );
            $stmt->execute([
                ':pid'    => $pid,
                ':name'   => $name,
                ':gender' => $gender,
                ':email'  => $email,
                ':phone'  => $phone,
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
<head><meta charset="UTF-8"><title>Add Patient</title></head>
<body>
<h1>Add Patient</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success): ?>
  <p>Patient successfully added.</p>
<?php endif; ?>

<p><a href="../public/patient/add_patient.html">Back to Patient Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
