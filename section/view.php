<?php

//start/resume current session
session_start();

//acquire model for starting query
require_once('../model/database.php');

//make sure that we are logged in because if not, the user will need to log in before purchasing a pair of shoes
if(isset($_SESSION['loggedin']) && isset($_SESSION['loggedin_as'])){
    $loggedin = $_SESSION['loggedin'];
    $loggedin_as = $_SESSION['loggedin_as'];
}

//get paramters to view this shoes details
$shoe_category = filter_input(INPUT_GET, 'shoe_category');
$shoe_type = filter_input(INPUT_GET, 'shoe_type');
$shoe = filter_input(INPUT_GET, 'shoe');
$section = filter_input(INPUT_GET, 'section');

///get product details
$query = "SELECT p.productID, p.productName, p.price, replace(p.productName, ' ', '-') as productFileName, replace(c.colorName, ' ', '-') as colorName, cat.categoryName 
            from product as p, color as c, category as cat 
            where p.colorID = c.colorID and p.categoryID = cat.categoryID and p.`section` = :section and productID = :shoe
            order by p.productName, p.categoryID ";

$statement = $db->prepare($query);
$statement->bindValue(':shoe', $shoe);
$statement->bindValue(':section', $section);
$statement->execute();
$shoe = $statement->fetch();
$statement->closeCursor();

// HEADER
include '../views/header.php';
?>

<main>
    <div class="card">
        <h3>
            <a href="../index.php">Home</a> > 
            <a href="index.php?section=<?php echo $section?>"><?php echo ucfirst($section) ?></a> > 
            <a href="category.php?section=<?php echo $section?>&shoe_category=<?php echo $shoe_category ?>">Category</a> > 
            <a href="type.php?section=<?php echo $section?>&shoe_category=<?php echo $shoe_category ?>&shoe_type=<?php echo $shoe_type ?>">Type</a> > 
            Product
        </h3>
        <form action="../purchase/index.php" method="post">
            <div class="dashboard">
                <!-- To check file name :P -->
                <!-- <p>../images/<?php echo $section?>_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png</p> -->
                
                <div class="side">
                    <div class="pic" style="width:100%;display:block;">
                        <div class="panel">
                            <img alt="shoe" style="height:512px; width:512px;"
                            src="../images/<?php echo $section?>_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png">
                        </div>
                    </div>

                    <div class="content">
                        <br><br>
                        <div class="panel">
                            <b>Name:</b> <?php echo $shoe['productName'] ?>
                        </div>

                        <div class="panel">
                            <b>Color:</b> <?php echo $shoe['colorName'] ?>
                        </div>

                        <div class="panel">
                            <b>Category:</b> <?php echo $shoe['categoryName'] ?>
                        </div>

                        <div class="panel">
                        <b>Price:</b> $<?php echo $shoe['price'] ?>
                        </div>
                    </div>   

                </div>
                <br><br>
                <!-- hidden elements to post parameters-->
                <input type="hidden" name="shoeID" value="<?php echo $shoe['productID'] ?>">
                <input type="hidden" name="section" value="<?php echo $section ?>">

                <?php
                    if((!isset($loggedin) || (isset($loggedin) && $loggedin == FALSE))) {
                ?>
                        <a href="../index.php" style="text-decoration: none;">
                            <input type="button" class="form_button buybutton" value="Login to Buy" />
                        </a>
                <?php } 
                    else { ?>
                        <input type="submit" class="form_button buybutton" value="Buy Now" />
                <?php } ?>
            </div>
        </form>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>