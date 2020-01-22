<?php
require_once 'common.php';

setcookie('admin', session_id(), time() + (30 * 60));

if (isset($_SESSION['admin']) && $_SESSION['admin'] == PASS) {
    if (!isset($_SESSION['edit'])) {
        $_SESSION['edit'] = array();
    }

    if (isset($_GET['id'])) {
        $_SESSION['edit'][] = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM Products WHERE id=?");
        $stmt->bindParam(1,$_GET['id'],PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $_POST['title'] = $row['title'];
            $_POST['description'] = $row['description'];
            $_POST['price'] = $row['price'];
            $_POST['image'] = $row['image'];
        }
    }

    $errors = array();
    $title = $description = $price = $image = "";
    if (isset($_POST['submit'])) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (empty($_POST['title'])) {
                $errors['title'] = trans("Title is required");
            } else {
                $title = strip_tags($_POST['title']);
                // check if title only contains letters and whitespace
                if (!preg_match("/^[a-zA-Z ]*$/", $title)) {
                    $errors['title'] = trans("Only letters and white space allowed");
                }
            }

            if (empty($_POST['description'])) {
                $errors['description'] = trans("Description is required");
            } else {
                $description = strip_tags($_POST['description']);
            }

            if (empty($_POST['price'])) {
                $errors['price'] = trans("Price is required");
            } else {
                $price = strip_tags($_POST['price']);
                // check if price contains double values
                if (!preg_match("/^[0-9]*\.[0-9]+$/", $price)) {
                    $errors['price'] = trans("Only numbers(double) value allowed");
                }
            }
        }

        if (isset($_POST['image'])) {
            $image = pathinfo($_FILES["browse"]["name"]);
            $_POST['image'] = $image['basename'];

            $target_dir = "images/";
            $target_file = $image['filename'];
            $imageFileType = strtolower(pathinfo($_FILES["browse"]["name"], PATHINFO_EXTENSION));
            $uniq = uniqid() . '.' . $imageFileType;

            if (move_uploaded_file($_FILES['browse']['tmp_name'], $target_dir . $uniq)) {
                if (isset($_SESSION['edit']) && !empty($_SESSION['edit'])) {
                    $stmt = $conn->prepare("UPDATE Products SET title=?, description=?, price=?, image=? WHERE id=?");
                    $stmt->bindParam(1,$title,PDO::PARAM_STR);
                    $stmt->bindParam(2,$description,PDO::PARAM_STR);
                    $stmt->bindParam(3,$price,PDO::PARAM_STR);
                    $stmt->bindParam(4,$uniq,PDO::PARAM_STR);
                    $stmt->bindParam(5,$_SESSION['edit'][0],PDO::PARAM_INT);
                } else {
                    $stmt = $conn->prepare("INSERT INTO Products(title, description, price, image) VALUES(?, ?, ?, ?)");
                    $stmt->bindParam(1,$title,PDO::PARAM_STR);
                    $stmt->bindParam(2,$description,PDO::PARAM_STR);
                    $stmt->bindParam(3,$price,PDO::PARAM_STR);
                    $stmt->bindParam(4,$uniq,PDO::PARAM_STR);
                }
                $stmt->execute();
                unset($_SESSION['edit']);
            }
        }
    }
} else {
    redirect('login.php');
}
?>
<html>
<head>
    <style>
        .error {
            color: #FF0000;
        }
    </style>
</head>
<body>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="<?= trans('Title') ?>"
           value="<?= isset($_POST['title']) ? $_POST['title'] : '' ?>"/>*<br/>
    <span class="error"><?= isset($errors['title']) ? $errors['title'] : ''; ?></span><br/>
    <input type="text" name="description" placeholder="<?= trans('Description') ?>"
           value="<?= isset($_POST['description']) ? $_POST['description'] : '' ?>"/>*<br/>
    <span class="error"><?= isset($errors['description']) ? $errors['description'] : ''; ?></span><br/>
    <input type="text" name="price" placeholder="<?= trans('Price') ?>"
           value="<?= isset($_POST['price']) ? $_POST['price'] : '' ?>"/>*<br/>
    <span class="error"><?= isset($errors['price']) ? $errors['price'] : ''; ?></span><br/>
    <input type="text" name="image" placeholder="<?= trans('Image') ?>"
           value="<?= isset($_POST['image']) ? $_POST['image'] : '' ?>"/>
    <input type="file" name="browse" id="browse" value="<?= trans('Browse') ?>"><br/><br/>
    <a href="products.php"><?= trans('Products') ?></a>
    <input type="submit" name="submit" value="<?= trans('Save') ?>">
</form>
</body>
</html>