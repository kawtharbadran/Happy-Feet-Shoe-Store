<?php
session_start();
require_once('../model/database.php');

if(isset($_SESSION['loggedin']) && isset($_SESSION['loggedin_as'])){
    $loggedin = $_SESSION['loggedin'];
    $loggedin_as = $_SESSION['loggedin_as'];
}

$shoe_category = filter_input(INPUT_GET, 'shoe_category');
$shoe_type = filter_input(INPUT_GET, 'shoe_type');
$shoe = filter_input(INPUT_GET, 'shoe');
///get all formal shoes 
$query = "select p.productID, p.productName, p.price, replace(p.productName, ' ', '-') as productFileName, replace(c.colorName, ' ', '-') as colorName, cat.categoryName 
            from product as p, color as c, category as cat 
            where p.colorID = c.colorID and p.categoryID = cat.categoryID and p.`section` = 'men' and productID = :shoe
            order by p.productName, p.categoryID ";
$statement = $db->prepare($query);
$statement->bindValue(':shoe', $shoe);
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
            <a href="index.php">Men</a> > 
            <a href="category.php?shoe_category=<?php echo $shoe_category ?>">Category</a> > 
            <a href="type.php?shoe_category=<?php echo $shoe_category ?>&shoe_type=<?php echo $shoe_type ?>">Type</a> > 
            Product
        </h3>
        <form action="../purchase/index.php" method="post">
            <div class="dashboard">
                <!-- To check file name :P -->
                <!-- <p>../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png</p> -->
                
                <div class="side">
                    <div class="pic">
                        <div class="panel">
                            <img alt="shoe" style="height:512px; width:512px;"
                            src="../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png">
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
                <br><br><br><br>
                <input type="hidden" name="shoeID" value="<?php echo $shoe['productID'] ?>">

                <?php
                    if((!isset($loggedin) || (isset($loggedin) && $loggedin == FALSE))) {
                ?>
                    <a href="../index.php" style="text-decoration: none;">
                        <input type="button" class="form_button buybutton" value="Login to Buy" />
                    </a>
                <?php } else { ?>
                    <input type="submit" class="form_button buybutton" value="Buy Now" />
                <?php } ?>
            </div>
        </form>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>