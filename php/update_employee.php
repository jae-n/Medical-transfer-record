<?php
require 'config.php';

$errors = [];
$success = false;
$updatedEmployee = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empID     = filter_input(INPUT_POST, 'empID', FILTER_VALIDATE_INT);
    $name      = isset($_POST['name']) ? trim($_POST['name']) : '';
    $startDate = $_POST['startDate'] ?? '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone     = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $shift     = isset($_POST['shift']) ? trim($_POST['shift']) : '';
    $role      = isset($_POST['role']) ? trim($_POST['role']) : '';

    $validShifts = ['day', 'night'];
    $validRoles  = ['Immediate Responder', 'Administrative Staff', 'Scheduled Staff'];

    // empID is always required
    if ($empID === false || $empID <= 0) {
        $errors[] = 'Invalid employee ID.';
    }

    // Validate only the fields that are actually provided (non-empty).
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid.';
    }

    if ($phone !== '' && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $errors[] = 'Phone must be 10 or 11 digits if provided.';
    }

    if ($shift !== '' && !in_array($shift, $validShifts, true)) {
        $errors[] = 'Invalid shift selected.';
    }

    if ($role !== '' && !in_array($role, $validRoles, true)) {
        $errors[] = 'Invalid role selected.';
    }

    // Build the SET clause dynamically.
    $setClauses = [];
    $params = [':empID' => $empID];

    if ($name !== '') {
        $setClauses[] = 'name = :name';
        $params[':name'] = $name;
    }

    if ($startDate !== '') {
        $setClauses[] = 'startDate = :startDate';
        $params[':startDate'] = $startDate;
    }

    if ($email !== '') {
        $setClauses[] = 'email = :email';
        $params[':email'] = $email;
    }

    if ($phone !== '') {
        $setClauses[] = 'phone = :phone';
        $params[':phone'] = $phone;
    }

    if ($shift !== '') {
        $setClauses[] = 'shift = :shift';
        $params[':shift'] = $shift;
    }

    if ($role !== '') {
        $setClauses[] = 'role = :role';
        $params[':role'] = $role;
    }

    if (empty($setClauses) && empty($errors)) {
        $errors[] = 'No fields provided to update. Please fill in at least one field.';
    }

    if (empty($errors)) {
        try {
            // Check employee exists
            $checkStmt = $pdo->prepare(
                "SELECT empID FROM Employee WHERE empID = :empID"
            );
            $checkStmt->execute([':empID' => $empID]);
            $exists = $checkStmt->fetch();

            if (!$exists) {
                $errors[] = "No employee found with ID $empID.";
            } else {
                // Build final SQL with only changed fields
                $sql = "UPDATE Employee SET " . implode(', ', $setClauses) . " WHERE empID = :empID";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Fetch the updated employee to display current data
                $fetchStmt = $pdo->prepare(
                    "SELECT empID, name, startDate, email, phone, shift, role
                     FROM Employee
                     WHERE empID = :empID"
                );
                $fetchStmt->execute([':empID' => $empID]);
                $updatedEmployee = $fetchStmt->fetch(PDO::FETCH_ASSOC);

                if ($updatedEmployee) {
                    $success = true;
                } else {
                    $errors[] = 'Employee was updated but could not be re-fetched.';
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
  <title>Update Employee</title>
</head>
<body>
<h1>Update Employee</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $updatedEmployee): ?>
  <p>Employee successfully updated:</p>
  <ul>
    <li><strong>ID:</strong> <?= sanitize_output($updatedEmployee['empID']) ?></li>
    <li><strong>Name:</strong> <?= sanitize_output($updatedEmployee['name']) ?></li>
    <li><strong>Start Date:</strong> <?= sanitize_output($updatedEmployee['startDate']) ?></li>
    <li><strong>Email:</strong> <?= sanitize_output($updatedEmployee['email']) ?></li>
    <li><strong>Phone:</strong> <?= sanitize_output($updatedEmployee['phone']) ?></li>
    <li><strong>Shift:</strong> <?= sanitize_output($updatedEmployee['shift']) ?></li>
    <li><strong>Role:</strong> <?= sanitize_output($updatedEmployee['role']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to update an employee.</p>
<?php endif; ?>

<p><a href="../public/employee/update_employee.html">Back to Update Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
