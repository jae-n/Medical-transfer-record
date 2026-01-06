<?php
require 'config.php';

$errors = [];
$success = false;
$deletedEmployee = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empID = filter_input(INPUT_POST, 'empID', FILTER_VALIDATE_INT);

    if ($empID === false || $empID <= 0) {
        $errors[] = 'Invalid employee ID.';
    }

    if (empty($errors)) {
        try {
            // Check if employee exists
            $checkStmt = $pdo->prepare(
                "SELECT empID, name, startDate, email, phone, shift, role
                 FROM Employee
                 WHERE empID = :empID"
            );
            $checkStmt->execute([':empID' => $empID]);
            $employee = $checkStmt->fetch();

            if (!$employee) {
                $errors[] = "No employee found with ID $empID.";
            } else {
                // Delete employee
                $deleteStmt = $pdo->prepare(
                    "DELETE FROM Employee WHERE empID = :empID"
                );
                $deleteStmt->execute([':empID' => $empID]);

                if ($deleteStmt->rowCount() > 0) {
                    $success = true;
                    $deletedEmployee = $employee;
                } else {
                    $errors[] = "Delete failed for employee ID $empID.";
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Cannot delete this employee because they are referenced (e.g., as a doctor or in other tables).';
            } else {
                $errors[] = 'Database error: ' . sanitize_output($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Delete Employee</title>
</head>
<body>
<h1>Delete Employee</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $deletedEmployee): ?>
  <p>Employee successfully deleted:</p>
  <ul>
    <li><strong>Employee ID:</strong> <?= sanitize_output($deletedEmployee['empID']) ?></li>
    <li><strong>Name:</strong> <?= sanitize_output($deletedEmployee['name']) ?></li>
    <li><strong>Start Date:</strong> <?= sanitize_output($deletedEmployee['startDate']) ?></li>
    <li><strong>Email:</strong> <?= sanitize_output($deletedEmployee['email']) ?></li>
    <li><strong>Phone:</strong> <?= sanitize_output($deletedEmployee['phone']) ?></li>
    <li><strong>Shift:</strong> <?= sanitize_output($deletedEmployee['shift']) ?></li>
    <li><strong>Role:</strong> <?= sanitize_output($deletedEmployee['role']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to delete an employee.</p>
<?php endif; ?>

<p><a href="../public/employee/delete_employee.html">Back to Delete Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
