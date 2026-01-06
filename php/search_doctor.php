<?php
require 'config.php';

// Read search filters from GET (safe defaults)
$docID = filter_input(INPUT_GET, 'docID', FILTER_VALIDATE_INT);
$empID = filter_input(INPUT_GET, 'empID', FILTER_VALIDATE_INT);
$specialization = trim($_GET['specialization'] ?? '');

// Build base query with JOIN to get employee name
$sql = "SELECT d.docID, d.empID, d.specialization, e.name AS empName
        FROM Doctor d
        LEFT JOIN Employee e ON d.empID = e.empID
        WHERE 1=1";
$params = [];

if ($docID !== null && $docID !== false) {
    $sql .= " AND d.docID = :docID";
    $params[':docID'] = $docID;
}

if ($empID !== null && $empID !== false) {
    $sql .= " AND d.empID = :empID";
    $params[':empID'] = $empID;
}

if ($specialization !== '') {
    $sql .= " AND d.specialization LIKE :specialization";
    $params[':specialization'] = '%' . $specialization . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Doctor Search Results</title>
</head>
<body>
  <h1>Doctor Search Results</h1>

  <?php if (empty($rows)): ?>
    <p>No doctors found matching your criteria.</p>
  <?php else: ?>
    <table border="1">
      <thead>
        <tr>
          <th>Doctor ID</th>
          <th>Employee ID</th>
          <th>Name</th>
          <th>Specialization</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= sanitize_output($row['docID']) ?></td>
            <td><?= sanitize_output($row['empID']) ?></td>
            <td><?= sanitize_output($row['empName'] ?? 'N/A') ?></td>
            <td><?= sanitize_output($row['specialization']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="../public/doctor/search_doctor.html">New Search</a></p>
  <p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>