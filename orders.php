<?php
require_once 'common.php';

$sql = "SELECT o.id, o.name_cust, o.email, SUM(p.price) as total FROM products p 
            INNER JOIN order_product op ON p.id=op.product_id 
            INNER JOIN orders o ON op.order_id=o.id 
            GROUP BY o.id";
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
        <td><?= trans('Id Order') ?></td>
        <td><?= trans('Customer Name') ?></td>
        <td><?= trans('Customer Email') ?></td>
        <td><?= trans('Total Order') ?></td>
    </tr>
    <?php if ((count($rows) > 0)): ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <a href="order.php?id=<?= $row['id'] ?>"><?= $row['id'] ?> <br/></a>
                </td>
                <td>
                    <?= $row['name_cust'] ?> <br/>
                </td>
                <td>
                    <?= $row['email'] ?> <br/>
                </td>
                <td>
                    <?= $row['total'] ?> <br/>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
</body>
</html>
