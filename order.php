<?php
require_once 'common.php';

if (isset($_GET['id'])) {
    $sql = "SELECT op.order_id, o.name_cust, o.email, op.product_id, p.id, p.title, p.description, p.price, p.image FROM products p 
                INNER JOIN order_product op ON p.id=op.product_id 
                INNER JOIN orders o ON op.order_id=o.id 
                WHERE op.order_id=" . $_GET['id'];
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $query = "SELECT SUM(p.price) as total from products p 
                    INNER JOIN order_product op ON p.id=op.product_id 
                    WHERE op.order_id=" . $_GET['id'];
    $stm = $conn->prepare($query);
    $stm->execute();
    $totals = $stm->fetchAll();
    foreach ($totals as $total) {
        $total['total'];
    }
}
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
<h3><?= trans('Details order') ?> <?= $_GET['id'] ?></h3>
<table>
    <tr>
        <td><?= trans('Order id') ?></td>
        <td><?= trans('Customer name') ?></td>
        <td><?= trans('Customer email') ?></td>
        <td><?= trans('Product id') ?></td>
        <td><?= trans('Product name') ?></td>
    </tr>
    <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?= $row['order_id'] ?> <br/>
                </td>
                <td>
                    <?= $row['name_cust'] ?> <br/>
                </td>
                <td>
                    <?= $row['email'] ?> <br/>
                </td>
                <td>
                    <?= $row['product_id'] ?> <br/>
                </td>
                <td>
                    <?= $row['title'] ?> <br/>
                </td>
            </tr>
        <?php endforeach; ?>
        <th colspan="5"><?= trans('Total') ?>:<?= $total['total']; ?></th>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
<h3><?= trans('Products details') ?></h3>
<table>
    <?php foreach ($rows as $row): ?>
        <tr>
            <td>
                <img src="images/<?= $row['image'] ?>" width="100" height="100" alt="<?= trans('Image product') ?>">
            </td>
            <td>
                <?= $row['title'] ?> <br/>
                <?= $row['description'] ?> <br/>
                <?= $row['price'] ?> <br/>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>