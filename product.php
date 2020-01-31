<?php
require_once 'common.php';

// if not login, redirect login page
if (!isset($_SESSION['admin']) && !$_SESSION['admin']) {
    redirect('login.php');
}

$errors = [];
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

        if (empty($_FILES['image']['name'])) {
            $errors['image'] = trans("Select image");
        } else {
            if (empty($errors)) {
                // check if the file is an image
                if (exif_imagetype($_FILES['image']['tmp_name']) !== false) {
                    $uniq = uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], TARGET_DIR . $uniq)) {
                        if (isset($_GET['id'])) {
                            $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, image = ? WHERE id = ?");
                            $stmt->execute([$title, $description, $price, $uniq, $_GET['id']]);
                        } else {
                            $stmt = $conn->prepare("INSERT INTO products(title, description, price, image) VALUES(?, ?, ?, ?)");
                            $stmt->execute([$title, $description, $price, $uniq]);
                        }
                    } else {
                        $errors['image'] = trans("Sorry, your image was not uploaded");
                    }
                } else {
                    $errors['image'] = trans("File is not an image");
                }
            }
        }
    }
}

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $title = $row['title'];
    $description = $row['description'];
    $price = $row['price'];
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
<form method="post"  enctype="multipart/form-data">
    <input type="text" name="title" placeholder="<?= trans('Title') ?>"
           value="<?= isset($_POST['title']) ? $_POST['title'] : $title ?>"/>*<br/>
    <span class="error"><?= isset($errors['title']) ? $errors['title'] : ''; ?></span><br/>
    <input type="text" name="description" placeholder="<?= trans('Description') ?>"
           value="<?= isset($_POST['description']) ? $_POST['description'] : $description ?>"/>*<br/>
    <span class="error"><?= isset($errors['description']) ? $errors['description'] : ''; ?></span><br/>
    <input type="text" name="price" placeholder="<?= trans('Price') ?>"
           value="<?= isset($_POST['price']) ? $_POST['price'] : $price ?>"/>*<br/>
    <span class="error"><?= isset($errors['price']) ? $errors['price'] : ''; ?></span><br/>
    <input type="file" name="image" id="image" value="<?= trans('Browse') ?>"><br/><br/>
    <span class="error"><?= isset($errors['image']) ? $errors['image'] : ''; ?></span><br/>
    <a href="products.php"><?= trans('Products') ?></a>
    <input type="submit" name="submit" value="<?= trans('Save') ?>">
</form>
</body>
</html>