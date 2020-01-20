<?php
require_once 'common.php';

session_start();

if (isset($_SESSION['cart'])) {
    if (!count($_SESSION['cart'])) {
        echo 'NO PRODUCTS IN CART';
    } else {
        if (isset($_GET['id'])) {
            $key = array_search($_GET['id'], $_SESSION['cart']);
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            header("Location: cart.php");
            exit();
        }
        $arr = array_fill(0, count($_SESSION['cart']), '?');
        $qMarks = implode(',', $arr);
        $sql = "SELECT * FROM  Products WHERE id IN($qMarks)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($_SESSION['cart']);
        $rows = $stmt->fetchAll();
    }
} else {
    echo "Success checkout!";
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

    if (!empty($_SESSION['cart'])) {
        foreach ($rows as $row) {
            $arr[] = $row['title'];
            $message = implode(',', $arr);
        }
    }
    /*
    if ($subject and $from and $message) {
        // need a mail server
        $mail = mail(TO, $subject, $message, $headers);
    }
    */

    if ($subject and $from and $message) {
        // need a mail server
        //$mail = mail(TO, $subject, $message, $headers);
        $mail = "da";
    }

    // to unset every time the checkout button is clicked
    unset($_SESSION['checkout']);

    // for cart checkout
    if (!isset($_SESSION['checkout'])) {
        $_SESSION['checkout'] = array();
    }

    $_SESSION['checkout'] = $_SESSION['cart'];


    if (!$emailErr or !$nameErr) {
        if (isset($_SESSION['checkout'])) {
            $sqlQuery = "INSERT INTO Orders(email, name_cust) VALUES('" . $email . "', '" . $name . "')";
            $smt = $conn->prepare($sqlQuery);

            if (count($smt->execute()) == 1) {
                $lastId = $conn->lastInsertId();
                foreach ($_SESSION['checkout'] as $id) {
                    $query = "INSERT INTO order_product(order_id, product_id) VALUES('" . $lastId . "', '"
                        . $id . "')";
                    $stm = $conn->prepare($query);
                    $stm->execute();
                }
            }
        }
    }
    if (isset($mail)) {
        //unset($_SESSION['cart']);
        session_unset();
        // or can be redirected to products page (index.php)
        header("Location: cart.php");
        exit();
    }
}
?>
    <html>
    <head></head>
    <body>
    <table>
        <?php if (!empty($rows) && count($rows) > 0): ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <img src="images/<?= $row['image'] ?>" width="100" height="100"
                             alt="<?= trans('Image product') ?>">
                    </td>
                    <td>
                        <?= $row['title'] ?> <br/>
                        <?= $row['description'] ?> <br/>
                        <?= $row['price'] ?> <br/>
                    </td>
                    <td>
                        <a href="cart.php?id=<?= $row['id'] ?>"><?= trans('Remove') ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
    <br/>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="name" placeholder="<?= trans('Name') ?>"
               value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>"/>
        <span class="error">* <?php echo $nameErr; ?></span><br/><br/>
        <input type="email" name="email" placeholder="<?= trans('Email') ?>"
               value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>"/>
        <span class="error">* <?php echo $emailErr; ?></span><br/><br/>
        <textarea name="comment" cols="20" rows="7" placeholder="<?= trans('Comment') ?>"></textarea><br/><br/>
        <input type="submit" name="submit" value="<?= trans('Checkout') ?>">
    </form>
    <a href="index.php"><?= trans('Go to products') ?></a>
    </body>
    </html>
<?php
