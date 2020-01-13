<?php
require_once 'common.php';


if (isset($_GET['delete'])) {
    $del = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Products WHERE id=$del");
    $stmt->execute(array($del));
    header("Location: products.php");
}
$stmt = $conn->prepare("SELECT * FROM Products");
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
                    <img src="images/<?= $row['image'] ?>" width="100" height="100" alt="Image product">
                </td>
                <td>
                    <?= $row['title'] ?> <br />
                    <?= $row['description'] ?> <br />
                    <?= $row['price'] ?> <br />
                </td>
                <td>
                    <a href="product.php?edit=<?= $row['id'] ?>"><?= trans('Edit') ?></a>
                </td>
                <td>
                    <a href="products.php?delete=<?= $row['id'] ?>"><?= trans('Delete') ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
<a href="product.php"><?= trans('Add') ?></a>
<a href="login.php"><?= trans('Logout') ?></a>
</body>
</html>
