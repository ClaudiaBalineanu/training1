<?php
require_once 'common.php';

if (!isset($_SESSION['admin']) && !$_SESSION['admin']) {
    redirect('login.php');
}

$sql = "SELECT * FROM contacts";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<table>
    <tr>
        <td><?= trans('Id contact') ?></td>
        <td><?= trans('Contact Name') ?></td>
        <td><?= trans('Contact Email') ?></td>
        <td><?= trans('Contact comment') ?></td>
    </tr>
    <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?= $row['id'] ?> <br/>
                </td>
                <td>
                    <?= $row['name'] ?> <br/>
                </td>
                <td>
                    <?= $row['email'] ?> <br/>
                </td>
                <td>
                    <?= $row['comment'] ?> <br/>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
</body>
</html>