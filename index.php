<?php
session_start();
//session_destroy();
// the file with the connection to the database
require_once 'common.php';

if (! isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// if is already in the session, don't insert again
if (isset($_GET['cart']) && ! in_array($_GET['cart'], $_SESSION['cart'])) {
    $_SESSION['cart'][] = $_GET['cart'];
}

if (!count($_SESSION['cart'])) {
    // prepare a query for selecting the products on the database
    $stmt = $conn->prepare("SELECT * FROM products");
    // execute the query
    $stmt->execute();
} else {
    $ids = $_SESSION['cart'];
    $arr = array_fill(0, count($ids), '?');
    $qMarks = implode(',', $arr);
    $sql = "SELECT * FROM Products WHERE id NOT IN($qMarks)";
    //print_r($ids);
    //print_r($sql); die();
    $stmt = $conn->prepare($sql);
    $stmt->execute($ids);
}
$rows = $stmt->fetchAll();
//session_unset();
//session_destroy();
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
                        <a href="index.php?cart=<?= $row['id'] ?>"><?= trans('Add') ?></a>
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
