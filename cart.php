<?php
require_once 'common.php';

session_start();

if (! isset($_SESSION['cart']) or $_SESSION['cart']==null) {
    echo 'NO PRODUCTS IN CART';
} else {

    if (isset($_GET['cart'])) {
        $key = array_search($_GET['cart'], $_SESSION['cart']);
        unset($_SESSION['cart'][$key]);
    }

    $ids = $_SESSION['cart'];
    $arr = array_fill(0, count($ids), '?');
    $qMarks = implode(',', $arr);
    $stmt = $conn->prepare("SELECT * FROM  Products WHERE id IN($qMarks)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
}

$nameErr = $emailErr = "";
$name = $email = $comment = "";
$message = $mess = "";

if (isset($_POST['submit'])) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
        } else {
            $name = strip_tags($_POST["name"]);
        }

        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = strip_tags($_POST["email"]);
        }

        if (empty($_POST["comment"])) {
            $comment = "";
        } else {
            $comment = strip_tags($_POST["comment"]);
        }
    }

    $subject = strip_tags($_POST['name']);
    $from = strip_tags($_POST['email']);
    $headers = "From:" . $from;

    if (! empty($_SESSION['cart'])) {
        foreach ($rows as $row) {
            $arr[] = $row['title'];
            $message = implode(',', $arr);
        }
    }

    if ($subject and $from and $message) {
        $mail = mail(TO, $subject, $message, $headers);
    }

    if (isset($mail)) {
        $mess = "Email send";
        // if the message was sent to redirect to the page with products
        //header('Location: index.php');
        //exit();
    } else {
        $mess = "Error";
    }
}
?>
<html>
    <head></head>
    <body>
        <table>
            <?php if (! empty($rows) && count($rows) > 0): ?>
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
                            <a href="cart.php?cart=<?= $row['id'] ?>"><?= trans('Remove') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
        <br />
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="name" placeholder="Name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>" />
            <span class="error">* <?php echo $nameErr;?></span><br /><br />
            <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>" />
            <span class="error">* <?php echo $emailErr;?></span><br /><br />
            <textarea name="comment" cols="20" rows="7"  placeholder="Comment"></textarea><br /><br />
            <input type="submit" name="submit" value="Checkout">
        </form>

        <a href="index.php"><?= trans('Go to products') ?></a>
        <!--  -->
        <p><?= $mess ?></p>
    </body>
</html>