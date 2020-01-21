<?php
require_once 'common.php';

if (isset($_SESSION['admin']) && $_SESSION['admin'] == PASS) {
    // get the time when the products page is accessed in seconds
    $now = time();
    // when session expire is < than the now (the 30 minutes have passed) destroy the session
    if ($now > $_SESSION['expire']) {
        session_destroy();
        // session has expired! redirect to login
        redirect('login.php');
    } else {
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("DELETE FROM Products WHERE id=" . $_GET['id']);
            $stmt->execute(array($_GET['id']));
            //when delete to refresh the page
            redirect('products.php');
        }
        $stmt = $conn->prepare("SELECT * FROM Products");
        $stmt->execute();
        $rows = $stmt->fetchAll();
    }
} else {
    redirect('login.php');
}
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
</body>
</html>