<?php
require_once 'common.php';

$errors = [];
$name = $email = $comment = "";
$message = "";

if (isset($_POST['submit'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($_POST['name'])) {
            $errors['name'] = trans('Name is required!');
        } else {
            $name = strip_tags($_POST['name']);
        }

        if (empty($_POST['email'])) {
            $errors['email'] = trans('Email is required!');
        } else {
            $email = strip_tags($_POST['email']);
        }

        if (empty($_POST['comment'])) {
            $comment = "";
        } else {
            $comment = strip_tags($_POST['comment']);
        }
    }

    if (!count($errors)) {
        $sqlQuery = "INSERT INTO contacts(name, email, comment) VALUES(?, ?, ?)";
        $smt = $conn->prepare($sqlQuery);
        $smt->execute([$name, $email, $comment]);
        $name = $email = $comment = '';
        $message = trans('Your request has been submitted');
    }
}
?>
<html>
<head></head>
<body>
<p></p>
<form method="post">
    <input type="text" name="name" placeholder="<?= trans('Name') ?>"
           value="<?= $name ?>"/>
    <span class="error">* <?= isset($errors['name']) ? $errors['name'] : ''; ?></span><br/><br/>
    <input type="email" name="email" placeholder="<?= trans('Email') ?>"
           value="<?= $email ?>"/>
    <span class="error">* <?= isset($errors['email']) ? $errors['email'] : ''; ?></span><br/><br/>
    <textarea name="comment" cols="20" rows="7"
              placeholder="<?= trans('Comment') ?>"><?= $comment ?></textarea><br/><br/>
    <input type="submit" name="submit" value="<?= trans('Submit') ?>">
</form>
<a href="index.php"><?= trans('Go to products') ?></a>
<p><?= $message ?></p>
</body>
</html>