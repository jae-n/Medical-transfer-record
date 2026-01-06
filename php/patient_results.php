<?php
require 'config.php';

// Get filters
$pid   = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT);
$name = trim($_GET['name'] ?? '');

$sql = "SELECT pid, name, gender, email, phone FROM Patient WHERE 1=1";
$params = [];

if ($pid !== null && $pid !== false) {
    $sql .= " AND pid = :pid";
    $params[':pid'] = $pid;
}

if ($name !== '') {
    $sql .= " AND name LIKE :name";
    $params[':name'] = '%' . $name . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Patient Search Results</title>
</head>
<body>
  <h1>Patient Search Results</h1>

  <?php if (empty($rows)): ?>
    <p>No patients found matching your criteria.</p>
  <?php else: ?>
    <table border="1">
      <thead>
        <tr>
          <th>Patient ID</th>
          <th>Name</th>
          <th>Gender</th>
          <th>Email</th>
          <th>Phone</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= sanitize_output($row['pid']) ?></td>
            <td><?= sanitize_output($row['name']) ?></td>
            <td><?= sanitize_output($row['gender']) ?></td>
            <td><?= sanitize_output($row['email']) ?></td>
            <td><?= sanitize_output($row['phone']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="../public/patient/search_patient.html">New Search</a></p>
  <p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
