<?php
require 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empID     = filter_input(INPUT_POST, 'empID', FILTER_VALIDATE_INT);
    $name      = trim($_POST['name'] ?? '');
    $startDate = $_POST['startDate'] ?? '';
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $shift     = trim($_POST['shift'] ?? '');
    $role      = trim($_POST['role'] ?? '');

    $validShifts = ['day', 'night'];
    $validRoles = ['Immediate Responder','Administrative Staff','Scheduled Staff'];

    if ($empID === false || $empID <= 0) {
        $errors[] = 'Invalid employee ID.';
    }
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($startDate === '') {
        $errors[] = 'Start date is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (!in_array($shift, $validShifts, true)) {
        $errors[] = 'Invalid shift selected.';
    }
    if (!in_array($role, $validRoles, true)) {
        $errors[] = 'Invalid role selected.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO Employee (empID, name, startDate, email, phone, shift, role)
                 VALUES (:empID, :name, :startDate, :email, :phone, :shift, :role)"
            );
            $stmt->execute([
                ':empID'     => $empID,
                ':name'      => $name,
                ':startDate' => $startDate,
                ':email'     => $email,
                ':phone'     => $phone,
                ':shift'     => $shift,
                ':role'      => $role,
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
<head><meta charset="UTF-8"><title>Add Employee</title></head>
<body>
<h1>Add Employee</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success): ?>
  <p>Employee successfully added.</p>
<?php endif; ?>

<p><a href="../public/employee/add_employee.html">Back to Employee Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
