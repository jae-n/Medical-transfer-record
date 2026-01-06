<?php
require 'config.php';

$errors = [];
$success = false;
$updatedPatient = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid    = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
    // Optional fields – may be empty strings
    $name   = isset($_POST['name'])   ? trim($_POST['name'])   : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $email  = isset($_POST['email'])  ? trim($_POST['email'])  : '';
    $phone  = isset($_POST['phone'])  ? trim($_POST['phone'])  : '';

    // pid is always required
    if ($pid === false || $pid <= 0) {
        $errors[] = 'Invalid patient ID.';
    }

    // Validate only if provided (non-empty)
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required if provided.';
    }

    if ($phone !== '' && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $errors[] = 'Phone must be 10 or 11 digits if provided.';
    }

    // Build dynamic SET clause
    $setClauses = [];
    $params = [':pid' => $pid];

    if ($name !== '') {
        $setClauses[] = 'name = :name';
        $params[':name'] = $name;
    }

    if ($gender !== '') {
        $setClauses[] = 'gender = :gender';
        $params[':gender'] = $gender;
    }

    if ($email !== '') {
        $setClauses[] = 'email = :email';
        $params[':email'] = $email;
    }

    if ($phone !== '') {
        $setClauses[] = 'phone = :phone';
        $params[':phone'] = $phone;
    }

    if (empty($setClauses) && empty($errors)) {
        $errors[] = 'No fields provided to update. Please fill in at least one field.';
    }

    if (empty($errors)) {
        try {
            // Check if patient exists
            $checkStmt = $pdo->prepare(
                "SELECT pid FROM Patient WHERE pid = :pid"
            );
            $checkStmt->execute([':pid' => $pid]);
            $exists = $checkStmt->fetch();

            if (!$exists) {
                $errors[] = "No patient found with ID $pid.";
            } else {
                // Perform the UPDATE with only provided fields
                $sql = "UPDATE Patient SET " . implode(', ', $setClauses) . " WHERE pid = :pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Fetch the updated patient data to display
                $fetchStmt = $pdo->prepare(
                    "SELECT pid, name, gender, email, phone
                     FROM Patient
                     WHERE pid = :pid"
                );
                $fetchStmt->execute([':pid' => $pid]);
                $updatedPatient = $fetchStmt->fetch(PDO::FETCH_ASSOC);

                if ($updatedPatient) {
                    $success = true;
                } else {
                    $errors[] = "Patient was updated but could not be re-fetched.";
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
  <title>Update Patient</title>
</head>
<body>
<h1>Update Patient</h1>

<?php if (!empty($errors)): ?>
  <h2>Errors</h2>
  <ul>
    <?php foreach ($errors as $err): ?>
      <li><?= sanitize_output($err) ?></li>
    <?php endforeach; ?>
  </ul>
<?php elseif ($success && $updatedPatient): ?>
  <p>Patient successfully updated:</p>
  <ul>
    <li><strong>ID:</strong> <?= sanitize_output($updatedPatient['pid']) ?></li>
    <li><strong>Name:</strong> <?= sanitize_output($updatedPatient['name']) ?></li>
    <li><strong>Gender:</strong> <?= sanitize_output($updatedPatient['gender']) ?></li>
    <li><strong>Email:</strong> <?= sanitize_output($updatedPatient['email']) ?></li>
    <li><strong>Phone:</strong> <?= sanitize_output($updatedPatient['phone']) ?></li>
  </ul>
<?php else: ?>
  <p>Please submit the form to update a patient.</p>
<?php endif; ?>

<p><a href="../public/patient/update_patient.html">Back to Update Form</a></p>
<p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
