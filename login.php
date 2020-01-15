<?php
require_once 'config.php';

if (isset($_POST['submit'])) {

    $data_missing = array();

    if (empty($_POST['username'])) {
        $data_missing[] = 'Username';
    } else {
        $username = strip_tags($_POST['username']);
    }

    if (empty($_POST['password'])) {
        $data_missing[] = 'Password';
    } else {
        $password = strip_tags($_POST['password']);
    }

    if (empty($data_missing)) {
        if (ADMIN !== $username OR PASS !== $password) {
            echo $mess = "Invalid Username or Password!";
        } else {
            header("Location: products.php");
            exit();
        }
    } else {
        echo "You need to enter: ";
        foreach ($data_missing as $missing) {
            echo $missing . ', ';
        }
    }
}
?>
<html>
<head></head>
<body>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="username" placeholder="Username"
           value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>"/><br/><br/>
    <input type="password" name="password" placeholder="Password"/><br/><br/>
    <input type="submit" name="submit" value="Login"/>
</form>
</body>
</html>