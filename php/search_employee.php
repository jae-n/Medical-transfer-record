<?php
require 'config.php';

// Read search filters from GET (safe defaults)
$empID = filter_input(INPUT_GET, 'empID', FILTER_VALIDATE_INT);
$name = trim($_GET['name'] ?? '');
$role = trim($_GET['role'] ?? '');
$shift = trim($_GET['shift'] ?? '');

// Build base query
$sql = "SELECT empID, name, startDate, email, phone, shift, role FROM Employee WHERE 1=1";
$params = [];

if ($empID !== null && $empID !== false) {
    $sql .= " AND empID = :empID";
    $params[':empID'] = $empID;
}

if ($name !== '') {
    $sql .= " AND name LIKE :name";
    $params[':name'] = '%' . $name . '%';
}

if ($role !== '') {
    $sql .= " AND role = :role";
    $params[':role'] = $role;
}

if ($shift !== '') {
    $sql .= " AND shift = :shift";
    $params[':shift'] = $shift;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Employee Search Results</title>
</head>
<body>
  <h1>Employee Search Results</h1>

  <?php if (empty($rows)): ?>
    <p>No employees found matching your criteria.</p>
  <?php else: ?>
    <table border="1">
      <thead>
        <tr>
          <th>Employee ID</th>
          <th>Name</th>
          <th>Start Date</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Shift</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= sanitize_output($row['empID']) ?></td>
            <td><?= sanitize_output($row['name']) ?></td>
            <td><?= sanitize_output($row['startDate']) ?></td>
            <td><?= sanitize_output($row['email']) ?></td>
            <td><?= sanitize_output($row['phone']) ?></td>
            <td><?= sanitize_output($row['shift']) ?></td>
            <td><?= sanitize_output($row['role']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="../public/employee/search_employee.html">New Search</a></p>
  <p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>