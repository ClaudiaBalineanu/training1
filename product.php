<?php
require_once 'common.php';

if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM Products WHERE id=" . $_GET['edit'];

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

if (! empty($_FILES)) {
    $img = pathinfo($_FILES["browse"]["name"]);

    $target_dir = "images/";
    $target_file = $img['filename'];
    $imageFileType = strtolower($img['extension']);

    $image = $_POST['image'];
    $image = $img['basename'];

}

$titleErr = $descriptionErr = $priceErr = "";
$title = $description = $price = "";
if (isset($_POST['submit'])) {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (empty($_POST['title'])) {
            $titleErr = "Title is required";
        } else {
            $title = strip_tags($_POST['title']);
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
        }

        if (empty($_POST['image'])) {
            $imageErr = "Image";
        } else {
            $image = $_POST['image'];
        }

    }


    $uniq = uniqid() . '.' . $imageFileType;

    if (move_uploaded_file($_FILES['browse']['tmp_name'], $target_dir . $uniq)) {
        //$_POST['image'] =
        //echo "The file ". basename( $_FILES["browse"]["name"]). " has been uploaded.";
        if (isset($_GET['edit'])) {
            $sql = "UPDATE Products SET title='" . $title . "', description='"
                . $description . "', price="
                . $price . ", image='"
                . $uniq . "')";


            print_r($sql); die();
            $stmt = $conn->prepare($sql);
            //$val = $target_dir . uniqid() . '.' . $imageFileType;
            $stmt->execute();
        } else {
            $sql = "INSERT INTO Products(title, description, price, image) VALUES('" . $title . "', '"
                . $description . "', "
                . $price . ", '"
                . $uniq . "')";
            //print_r($sql); die();
            $stmt = $conn->prepare($sql);
            //$val = $target_dir . uniqid() . '.' . $imageFileType;
            $stmt->execute();
        }
    }



}


?>
<html>
<head></head>
    <body>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '' ?>" />
            <span class="error">* <?php echo $titleErr;?></span><br /><br />
            <input type="text" name="description" placeholder="Description" value="<?php echo isset($_POST['description']) ? $_POST['description'] : '' ?>" />
            <span class="error">* <?php echo $descriptionErr;?></span><br /><br />
            <input type="text" name="price" placeholder="Price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : '' ?>" />
            <span class="error">* <?php echo $priceErr;?></span><br /><br />
            <input type="text" name="image" placeholder="Image" value="<?php echo isset($_POST['image']) ? $_POST['image'] : '' ?>"  />

            <input type="file" name="browse" id="browse" value="Browse" > <br><br>
            <a href="products.php"><?= trans('Products') ?></a>
            <input type="submit" name="submit" value="Save">
        </form>


        <!--  -->

    </body>
</html>
<?php
/*
print_r($title);
print_r("*******");
print_r($img);
print_r("*******");
print_r($_POST['image']);
print_r("*******");
*/