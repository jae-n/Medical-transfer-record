<?php
require 'config.php';

$errors = [];
$success = false;
$rid = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate rid as an integer
    $rid = filter_input(INPUT_POST, 'rid', FILTER_VALIDATE_INT);

    if ($rid === false || $rid <= 0) {
        $errors[] = 'Invalid Record ID.';
    } else {
        try {
            // Assumes you have a $pdo object in config.php
            $stmt = $pdo->prepare('DELETE FROM record WHERE rid = :rid');
            $stmt->execute([':rid' => $rid]);

            if ($stmt->rowCount() > 0) {
                $success = true;
            } else {
                $errors[] = 'No record found with that ID.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Delete Medical Record</title>
</head>
<body>
    <h1>Delete Medical Record</h1>

    <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
        <p>No data submitted.</p>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <h3>Errors:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($success): ?>
            <p>Record with ID <?php echo htmlspecialchars($rid); ?> was successfully deleted.</p>
        <?php endif; ?>
    <?php endif; ?>

    <p><a href="../public/record/delete_record.html">Back to Delete Record</a></p>
    <p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
