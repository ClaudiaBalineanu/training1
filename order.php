<?php
require_once 'common.php';

if (!isset($_SESSION['admin']) && !$_SESSION['admin']) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $sql = "SELECT o.id, o.name_cust, o.email FROM orders o WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['id']]);
    $rows = $stmt->fetchAll();

    $query = "SELECT p.title, p.description, p.price, p.image
                    FROM products p 
                    INNER JOIN order_product op ON p.id = op.product_id 
                    WHERE op.order_id = ?";
    $stm = $conn->prepare($query);
    $stm->execute([$_GET['id']]);
    $products = $stm->fetchAll();
    $sum=0;
    foreach ($products as $product) {
        $sum += $product['price'];
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
    </tr>
    <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?= $row['id'] ?> <br/>
                </td>
                <td>
                    <?= $row['name_cust'] ?> <br/>
                </td>
                <td>
                    <?= $row['email'] ?> <br/>
                </td>
            </tr>
        <?php endforeach; ?>
        <th colspan="3"><?= trans('Total') ?>:<?= $sum; ?></th>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
<h3><?= trans('Products details') ?></h3>
<table>
    <?php foreach ($products as $product): ?>
        <tr>
            <td>
                <img src="images/<?= $product['image'] ?>" width="100" height="100" alt="<?= trans('Image product') ?>">
            </td>
            <td>
                <?= $product['title'] ?> <br/>
                <?= $product['description'] ?> <br/>
                <?= $product['price'] ?> <br/>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>