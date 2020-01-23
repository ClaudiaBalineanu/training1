<?php
// the file with the connection to the database
require_once 'common.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// if is already in the session, don't insert again
if (isset($_GET['id']) && !in_array($_GET['id'], $_SESSION['cart'])) {
    $_SESSION['cart'][] = $_GET['id'];
    redirect('index.php');
}

if (!count($_SESSION['cart'])) {
    // prepare a query for selecting the products on the database
    $stmt = $conn->prepare("SELECT * FROM products");
} else {
    $arr = array_fill(0, count($_SESSION['cart']), '?');
    $qMarks = implode(',', $arr);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id NOT IN($qMarks)");
}
$stmt->execute($_SESSION['cart']);
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
                    <a href="index.php?id=<?= $row['id'] ?>"><?= trans('Add') ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?= trans('No data') ?>
    <?php endif; ?>
</table>
<a href="cart.php"><?= trans('Go To Cart') ?></a>
</body>
</html>
