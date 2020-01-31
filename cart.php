<?php
require_once 'common.php';

if (isset($_SESSION['cart'])) {
    if (count($_SESSION['cart'])) {
        if (isset($_GET['id'])) {
            $keySession = array_search($_GET['id'], $_SESSION['cart']);
            if (isset($keySession)) {
                unset($_SESSION['cart'][$keySession]);
            }
            redirect('cart.php');
        }
        if (!empty($_SESSION['cart'])) {
            $arr = array_fill(0, count($_SESSION['cart']), '?');
            $qMarks = implode(',', $arr);
            $stmt = $conn->prepare("SELECT * FROM  products WHERE id IN($qMarks)");
            // reindexing the cart with array_values
            $stmt->execute(array_values($_SESSION['cart']));
            $rows = $stmt->fetchAll();
        }
    } else {
        $mess = trans('No products in cart!');
    }
} else {
    $mess = trans('Successful checkout!');
}

$errors = [];
$name = $email = $comment = "";
$message = "";

if (isset($_POST['submit'])) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($_POST['name'])) {
            $errors['name'] = trans('Name is required');
        } else {
            $name = strip_tags($_POST['name']);
        }

        if (empty($_POST['email'])) {
            $errors['email'] = trans('Email is required');
        } else {
            $email = strip_tags($_POST['email']);
        }

        if (empty($_POST['comment'])) {
            $comment = "";
        } else {
            $comment = strip_tags($_POST['comment']);
        }
    }

    if (!isset($errors['name']) && !isset($errors['email'])) {
        if (!empty($_SESSION['cart'])) {
            $subject = trans('Email checkout');
            $from = strip_tags($_POST['email']);

            $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) &&  $_SERVER['HTTPS'] == 1 ? 'https' : 'http';

            $message = '<html><head></head><body>';
            $message .= '<p>' . trans('Name') . ': ' . $name . '<br/>'
                . trans('Email') . ': ' . $email . '<br/>'
                . trans('Comment') . ': ' . $comment . '</p><table>';
            if (!empty($rows) && count($rows) > 0) {
                foreach ($rows as $row) {
                    $message .= '<tr><td><img src="' . $protocol . '://' . $_SERVER['HTTP_HOST'] . '/images/' . $row['image'] .
                        '" width="100" height="100" alt="' . trans('Image product') . '"></td>';
                    $message .= '<td>' . $row['title'] . '<br/>' .
                        $row['description'] . '<br/>' .
                        $row['price'] . '<br/></td></tr>';
                }
            }
            $message .= '</table></body></html>';

            // set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . $from . "\r\n";

            // need a mail server
            $mail = mail(TO, $subject, $message, $headers);

            $sqlQuery = "INSERT INTO orders(email, name_cust) VALUES(?, ?)";
            $smt = $conn->prepare($sqlQuery);
            if (count($smt->execute([$email, $name])) == 1) {
                $lastId = $conn->lastInsertId();
                foreach ($_SESSION['cart'] as $id) {
                    $query = "INSERT INTO order_product(order_id, product_id) VALUES(?, ?)";
                    $stm = $conn->prepare($query);
                    $stm->execute([$lastId, $id]);
                }
            }

            unset($_SESSION['cart']);
            redirect('cart.php');
        } else {
            $mess = trans("Cart can't be empty");
        }
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
           value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>"/>
    <span class="error">* <?= isset($errors['name']) ? $errors['name'] : ''; ?></span><br/><br/>
    <input type="email" name="email" placeholder="<?= trans('Email') ?>"
           value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>"/>
    <span class="error">* <?= isset($errors['email']) ? $errors['email'] : ''; ?></span><br/><br/>
    <textarea name="comment" cols="20" rows="7"
              placeholder="<?= trans('Comment') ?>"><?= isset($_POST['comment']) ? $_POST['comment'] : '' ?></textarea><br/><br/>
    <input type="submit" name="submit" value="<?= trans('Checkout') ?>">
</form>
<a href="index.php"><?= trans('Go to products') ?></a>
</body>
</html>