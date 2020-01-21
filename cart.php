<?php
require_once 'common.php';

if (isset($_SESSION['cart'])) {
    if (count($_SESSION['cart'])) {
        if (isset($_GET['id'])) {
            $keySession = array_search($_GET['id'], $_SESSION['cart']);
            if (isset($keySession)) {
                unset($_SESSION['cart'][$keySession]);
                // remake the session array (in unset 2=>2, the 3=>3 will become 2=>3)
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            redirect('cart.php');
        }
        if (!empty($_SESSION['cart'])) {
            $arr = array_fill(0, count($_SESSION['cart']), '?');
            $qMarks = implode(',', $arr);
            $stmt = $conn->prepare("SELECT * FROM  Products WHERE id IN($qMarks)");
            $stmt->execute($_SESSION['cart']);
            $rows = $stmt->fetchAll();
        }
    } else {
        $mess = trans('NO PRODUCTS IN CART');
    }
} else {
    $mess = trans('Successful checkout!');
}

$errors = array();
$name = $email = $comment = "";
$message = "";

if (isset($_POST['submit'])) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["name"])) {
            $errors['name'] = trans('Name is required');
        } else {
            $name = strip_tags($_POST["name"]);
        }

        if (empty($_POST["email"])) {
            $errors['email'] = trans('Email is required');
        } else {
            $email = strip_tags($_POST["email"]);
        }

        if (empty($_POST["comment"])) {
            $comment = "";
        } else {
            $comment = strip_tags($_POST["comment"]);
        }
    }

    $subject = trans("Email checkout");
    $from = strip_tags($_POST['email']);

    $message = '<html><head></head><body><table>';
    if (!empty($rows) && count($rows) > 0) {
        foreach ($rows as $row) {
            $message .= '<tr><td><img src="images/' . $row['image'] .
                '" width="100" height="100" alt="' . trans('Image product') . '"></td>';
            $message .= '<td>' . $row['title'] . '<br/>' .
                $row['description'] . '<br/>' .
                $row['price'] . '<br/></td></tr>';
        }
    } else {
        echo $mess;
    }
    $message .= '</table></body></html>';
    // use wordwrap() if lines are longer than 70 characters
    $message = wordwrap($message, 70);

    // set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . $from . "\r\n";

    if ($subject && $from && $message) {
        // need a mail server and i don't have one so i just set a variable with a string !!!
        // $mail = mail(TO, $subject, $message, $headers);
        $mail = "yes";
    }

    if (!isset($_SESSION['checkout'])) {
        $_SESSION['checkout'] = array();
    }

    $_SESSION['checkout'] = $_SESSION['cart'];
    if (!isset($errors['name']) && !isset($errors['email'])) {
        if (!empty($_SESSION['checkout'])) {
            $sqlQuery = "INSERT INTO orders(email, name_cust) VALUES(?,?)";
            $smt = $conn->prepare($sqlQuery);
            $smt->bindParam(1, $email, PDO::PARAM_STR, 50);
            $smt->bindParam(2, $name, PDO::PARAM_STR, 50);

            if (count($smt->execute()) == 1) {
                $lastId = $conn->lastInsertId();
                foreach ($_SESSION['checkout'] as $id) {
                    $query = "INSERT INTO order_product(order_id, product_id) VALUES(?,?)";
                    $stm = $conn->prepare($query);
                    $stm->bindParam(1, $lastId, PDO::PARAM_INT);
                    $stm->bindParam(2, $id, PDO::PARAM_INT);
                    $stm->execute();
                }
            }
        }
    }
    if (isset($mail)) {
        unset($_SESSION['cart']);
        // or can be redirected to products page (index.php)
        redirect('cart.php');
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
    <?php else: ?>
        <?= $mess; ?>
    <?php endif; ?>
</table>
<br/>
<form method="post">
    <input type="text" name="name" placeholder="<?= trans('Name') ?>"
           value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>"/>
    <span class="error">* <?php echo isset($errors['name']) ? $errors['name'] : ''; ?></span><br/><br/>
    <input type="email" name="email" placeholder="<?= trans('Email') ?>"
           value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>"/>
    <span class="error">* <?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span><br/><br/>
    <textarea name="comment" cols="20" rows="7"
              placeholder="<?= trans('Comment') ?>"><?php echo isset($_POST['comment']) ? $_POST['comment'] : '' ?></textarea><br/><br/>
    <input type="submit" name="submit" value="<?= trans('Checkout') ?>">
</form>
<a href="index.php"><?= trans('Go to products') ?></a>
</body>
</html>