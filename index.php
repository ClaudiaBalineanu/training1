<?php
session_start();
// the file with the connection to the database
require_once 'common.php';

// prepare a query for selecting the products on the database
$stmt = $conn->prepare("SELECT * FROM products");
// execute the query
$stmt->execute();

$var = $stmt->fetchAll();
?>
<html>
<head></head>
<body>
<?php if (count($var) > 0): ?>
    <?php foreach ($var as $row): ?>
        <?= trans('Prod') ?> <?= $row['title'] ?> <br />
    <?php endforeach; ?>
<?php else: ?>
    No data
<?php endif; ?>
</body>
</html>