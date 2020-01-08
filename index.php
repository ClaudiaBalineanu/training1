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
    <table>
        <?php if (count($var) > 0): ?>
            <?php foreach ($var as $row): ?>
            <tr>
                <td>
                    <?= '<img src="images/' . $row['image'] . '" width="100" height="100" alt="Image product">' ?>
                </td>
                <td>
                    <?= $row['title'] ?> <br /><!-- </?= trans('Prod') ?>  -->
                    <?= $row['description'] ?> <br />
                    <?= $row['price'] ?> <br />
                </td>
                <td>
                    <a href="index.php?cart=<?= $row['id'] ?>">Add</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
             <?= trans('No data') ?>
        <?php endif; ?>
    </table>
    <a href="cart.php">Go To Cart</a>
</body>
</html>
<?php
if (! isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
if (isset($_GET['cart'])) {// nup sa nu mai adauge acelasi id de mai multe ori
    array_push($_SESSION['cart'], $_GET['cart']);
}
