<?php
require_once 'common.php';

// for logout
if (isset($_SESSION['admin'])) {
    unset($_SESSION['admin']);
}

if (isset($_POST['submit'])) {

    $errors = [];

    if (empty($_POST['username'])) {
        $errors[] = trans('Insert Username! ');
    } elseif (ADMIN !== $_POST['username']) {
        $errors[] = trans("Invalid Username! ");
    } else {
        $username = strip_tags($_POST['username']);
    }

    if (empty($_POST['password'])) {
        $errors[] = trans('Insert Password! ');
    } elseif (PASS !== $_POST['password']) {
        $errors[] = trans("Invalid Password! ");
    } else {
        $password = strip_tags($_POST['password']);
    }

    if (empty($errors)) {
            // set session admin with the password
            $_SESSION['admin'] = true;
            redirect('products.php');
    }
}
?>
<html>
<head></head>
<body>
<?php if (isset($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <?= $error; ?>
    <?php endforeach; ?>
<?php endif; ?>
<form method="post">
    <input type="text" name="username" placeholder="<?= trans('Username') ?>"
           value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>"/><br/><br/>
    <input type="password" name="password" placeholder="<?= trans('Password') ?>"/><br/><br/>
    <input type="submit" name="submit" value="<?= trans('Login') ?>"/>
</form>
</body>
</html>