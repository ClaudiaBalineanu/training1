<?php
//require_once 'config.php';
require_once 'common.php';

if (isset($_SESSION['admin'])) {
    session_unset();
} else {
    $_SESSION['admin'] = array();
}

if (isset($_POST['submit'])) {

    $errors = array();

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
        if (ADMIN == $username && PASS == $password) {
            // set session admin with the password
            $_SESSION['admin'] = $password;
            // get the time in seconds
            $_SESSION['start'] = time();
            // ending a session in 30 minutes from the starting time.
            $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);
            redirect('products.php');
        }
    } else {
        foreach ($errors as $error) {
            echo $error;
        }
    }
}
?>
<html>
<head></head>
<body>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="username" placeholder="<?= trans('Username') ?>"
           value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>"/><br/><br/>
    <input type="password" name="password" placeholder="<?= trans('Password') ?>"/><br/><br/>
    <input type="submit" name="submit" value="<?= trans('Login') ?>"/>
</form>
</body>
</html>