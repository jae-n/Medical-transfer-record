<?php
require 'config.php';

// Read filters safely
$rid     = filter_input(INPUT_GET, 'rid', FILTER_VALIDATE_INT);
$pid     = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT);
$docID   = filter_input(INPUT_GET, 'docID', FILTER_VALIDATE_INT);
$hosp    = trim($_GET['hospitalName'] ?? '');
$diag    = trim($_GET['diagnosis'] ?? '');

// Build query dynamically
$sql = "SELECT rid, pid, docID, diagnosis, hospitalName FROM Record WHERE 1=1";
$params = [];

if ($rid !== null && $rid !== false) {
    $sql .= " AND rid = :rid";
    $params[':rid'] = $rid;
}

if ($pid !== null && $pid !== false) {
    $sql .= " AND pid = :pid";
    $params[':pid'] = $pid;
}

if ($docID !== null && $docID !== false) {
    $sql .= " AND docID = :docID";
    $params[':docID'] = $docID;
}

if ($hosp !== '') {
    $sql .= " AND hospitalName LIKE :hosp";
    $params[':hosp'] = '%' . $hosp . '%';
}

if ($diag !== '') {
    $sql .= " AND diagnosis LIKE :diag";
    $params[':diag'] = '%' . $diag . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Record Search Results</title>
</head>
<body>

<h1>Record Search Results</h1>

<?php if (empty($rows)): ?>
    <p>No matching medical records found.</p>
<?php else: ?>
    <table border="1">
        <thead>
            <tr>
                <th>Record ID</th>
                <th>Patient ID</th>
                <th>Doctor ID</th>
                <th>Diagnosis</th>
                <th>Hospital Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= sanitize_output($row['rid']) ?></td>
                    <td><?= sanitize_output($row['pid']) ?></td>
                    <td><?= sanitize_output($row['docID']) ?></td>
                    <td><?= sanitize_output($row['diagnosis']) ?></td>
                    <td><?= sanitize_output($row['hospitalName']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="../public/record/search_record.html">New Search</a></p>
<p><a href="../public/index.html">Back to Home</a></p>

</body>
</html>
