<?php
require_once 'common.php';

if (!isset($_SESSION['admin']) && !$_SESSION['admin']) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    //when delete to refresh the page
    redirect('products.php');
}
$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<html>
<head></head>
<body>
<table>
    <?php if ((count($rows) > 0)): ?>
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
                <td>
                    <a href="product.php?id=<?= $row['id'] ?>"><?= trans('Edit') ?></a>
                </td>
                <td>
                    <a href="products.php?id=<?= $row['id'] ?>"><?= trans('Delete') ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
<a href="product.php"><?= trans('Add') ?></a>
<a href="login.php"><?= trans('Logout') ?></a>
<a href="orders.php"><?= trans('Orders') ?></a>
</body>
</html>