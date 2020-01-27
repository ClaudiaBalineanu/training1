<?php
require_once 'common.php';

// if not login, redirect login page
if (!isset($_SESSION['admin']) && !$_SESSION['admin']) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $_SESSION['edit'] = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $_POST['title'] = $row['title'];
        $_POST['description'] = $row['description'];
        $_POST['price'] = $row['price'];
        $_POST['image'] = $row['image'];
    }
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

        if (empty($_FILES["browse"]["name"])) {
            $errors['image'] = trans("Select image!");
        } else {
            // original uploaded name file
            $image = pathinfo($_FILES["browse"]["name"]);
            // e.g. basename = 1.jpg
            $_POST['image'] = $image['basename'];

            // e.g. filename = 1
            $target_file = $image['filename'];
            // e.g. extension = jpg
            $imageFileType = $image['extension'];
            $uniq = uniqid() . '.' . $imageFileType;
            if (empty($errors)) {
                // temporary file name on server
                if (move_uploaded_file($_FILES['browse']['tmp_name'], TARGET_DIR . $uniq)) {
                    if (isset($_SESSION['edit'])) {
                        $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, image = ? WHERE id = ?");
                        $stmt->execute([$title, $description, $price, $uniq, $_SESSION['edit']]);
                    } else {
                        $stmt = $conn->prepare("INSERT INTO products(title, description, price, image) VALUES(?, ?, ?, ?)");
                        $stmt->execute([$title, $description, $price, $uniq]);
                    }
                    unset($_SESSION['edit']);
                }
            }
        }
    }
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
    <span class="error"><?= isset($errors['image']) ? $errors['image'] : ''; ?></span><br/>
    <a href="products.php"><?= trans('Products') ?></a>
    <input type="submit" name="submit" value="<?= trans('Save') ?>">
</form>
</body>
</html>