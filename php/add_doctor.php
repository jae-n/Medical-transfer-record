<?php
require 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docID          = filter_input(INPUT_POST, 'docID', FILTER_VALIDATE_INT);
    $specialization = trim($_POST['specialization'] ?? '');

    if ($docID === false || $docID <= 0) {
        $errors[] = 'Invalid doctor ID.';
    }

    if ($specialization === '') {
        $errors[] = 'Specialization is required.';
    }

    // Only check employee if basic validation passed
    if (empty($errors)) {
        // Check if an employee with this ID already exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM Employee WHERE empID = :id");
        $check->execute([':id' => $docID]);
        $exists = $check->fetchColumn();

        if ($exists == 0) {
            $errors[] = 'No employee with that ID exists. Please add the employee first.';
        }
    }

    if (empty($errors)) {
        try {
            // Use docID as both docID and empID
            $stmt = $pdo->prepare(
                "INSERT INTO Doctor (docID, empID, specialization)
                 VALUES (:docID, :empID, :specialization)"
            );
            $stmt->execute([
                ':docID'          => $docID,
                ':empID'          => $docID,
                ':specialization' => $specialization,
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
<head><meta charset="UTF-8"><title>Register Doctor</title></head>
<body>
<h1>Register Doctor</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success): ?>
  <p>Doctor successfully registered.</p>
<?php endif; ?>

<p><a href="../public/doctor/add_doctor.html">Back to Doctor Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
