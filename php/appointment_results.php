<?php
require 'config.php';

// Read optional search filter from GET
$appID = filter_input(INPUT_GET, 'appID', FILTER_VALIDATE_INT);

// Build base query
$sql = "SELECT appID, appDate, pid, docID, status FROM Appointment WHERE 1=1";
$params = [];

if ($appID !== null && $appID !== false) {
    $sql .= " AND appID = :appID";
    $params[':appID'] = $appID;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Appointment Search Results</title>
</head>
<body>
  <h1>Appointment Search Results</h1>

  <?php if ($appID !== null && $appID !== false): ?>
      <p>Showing results for Appointment ID: <?= sanitize_output($appID) ?></p>
  <?php else: ?>
      <p>Showing all appointments.</p>
  <?php endif; ?>

  <?php if (empty($rows)): ?>
    <p>No appointments found.</p>
  <?php else: ?>
    <table border="1">
      <thead>
        <tr>
          <th>Appointment ID</th>
          <th>Date</th>
          <th>Patient ID</th>
          <th>Doctor ID</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= sanitize_output($row['appID']   ?? '') ?></td>
            <td><?= sanitize_output($row['appDate'] ?? '') ?></td>
            <td><?= sanitize_output($row['pid']     ?? '') ?></td>
            <td><?= sanitize_output($row['docID']   ?? '') ?></td>
            <td><?= sanitize_output($row['status']  ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="../public/appointment/search_appointment.html">New Search</a></p>
  <p><a href="../public/appointment/schedule_appointment.html">Schedule New Appointment</a></p>
  <p><a href="../public/index.html">Back to Home</a></p>
</body>
</html>
