<?php
require_once 'common.php';

session_start();

if (!isset($_SESSION['edit'])) {
    $_SESSION['edit'] = array();
}

if (isset($_GET['id'])) {
    $_SESSION['edit'][] = $_GET['id'];
    $sql = "SELECT * FROM Products WHERE id=" . $_GET['id'];
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $_POST['title'] = $row['title'];
        $_POST['description'] = $row['description'];
        $_POST['price'] = $row['price'];
        $_POST['image'] = $row['image'];
    }
}

$titleErr = $descriptionErr = $priceErr = $imageErr = "";
$title = $description = $price = $image = "";
if (isset($_POST['submit'])) {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (empty($_POST['title'])) {
            $titleErr = "Title is required";
        } else {
            $title = strip_tags($_POST['title']);
            // check if title only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/", $title)) {
                $titleErr = "Only letters and white space allowed";
            }
        }

        if (empty($_POST['description'])) {
            $descriptionErr = "Description is required";
        } else {
            $description = strip_tags($_POST['description']);
        }

        if (empty($_POST['price'])) {
            $priceErr = "Price is required";
        } else {
            $price = strip_tags($_POST['price']);
            // check if price contains double values
            if (!preg_match("/^[0-9]*\.[0-9]+$/", $price)) {
                $priceErr = "Only numbers(double) value allowed";
            }
        }

    }


    if(isset($_POST['image'])) {

        $image = pathinfo($_FILES["browse"]["name"]);
        $_POST['image'] = $image['basename'];

        $target_dir = "images/";
        $target_file = $image['filename'];
        $imageFileType = strtolower($image['extension']);

        $uniq = uniqid() . '.' . $imageFileType;

        if (move_uploaded_file($_FILES['browse']['tmp_name'], $target_dir . $uniq)) {
            if (isset($_SESSION['edit']) && !empty($_SESSION['edit'])) {
                $sql = "UPDATE Products SET title='" . $title . "', description='"
                    . $description . "', price='"
                    . $price . "', image='"
                    . $uniq . "' WHERE id=" . $_SESSION['edit'][0];
            } else {
                $sql = "INSERT INTO Products(title, description, price, image) VALUES('" . $title . "', '"
                    . $description . "', '"
                    . $price . "', '"
                    . $uniq . "')";
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            session_unset();
        }
    }
}
?>
<html>
<head>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title"
           value="<?php echo isset($_POST['title']) ? $_POST['title'] : '' ?>"/>*<br/>
    <span class="error"><?php echo $titleErr; ?></span><br/>
    <input type="text" name="description" placeholder="Description"
           value="<?php echo isset($_POST['description']) ? $_POST['description'] : '' ?>"/>*<br/>
    <span class="error"><?php echo $descriptionErr; ?></span><br/>
    <input type="text" name="price" placeholder="Price"
           value="<?php echo isset($_POST['price']) ? $_POST['price'] : '' ?>"/>*<br/>
        <span class="error"><?php echo $priceErr; ?></span><br/>
    <input type="text" name="image" placeholder="Image"
           value="<?php echo isset($_POST['image']) ? $_POST['image'] : '' ?>"/>
    <input type="file" name="browse" id="browse" value="Browse"><br/><br/>
    <a href="products.php"><?= trans('Products') ?></a>
    <input type="submit" name="submit" value="Save">
</form>
</body>
</html>