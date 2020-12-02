<?php
session_start();
require_once('../model/database.php');

$shoe_category = filter_input(INPUT_GET, 'shoe_category');
$shoe_type = filter_input(INPUT_GET, 'shoe_type');
///get all formal shoes 
$query = "select p.productID, p.productName, replace(p.productName, ' ', '-') as productFileName, replace(c.colorName, ' ', '-') as colorName, cat.categoryName 
            from product as p, color as c, category as cat 
            where p.colorID = c.colorID and p.categoryID = :shoe_category and p.categoryID = cat.categoryID and p.`section` = 'men' and productName = :shoe_type
            order by p.productName, p.categoryID ";
$statement = $db->prepare($query);
$statement->bindValue(':shoe_category', $shoe_category);
$statement->bindValue(':shoe_type', $shoe_type);
$statement->execute();
$shoes = $statement->fetchAll();
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
            Type 
        </h3>
        <div class="dashboard">
            <?php foreach($shoes as $shoe){?>
                <div class="shoeitem">
                    <!-- To check file name :P -->
                    <!-- <p>../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png</p> -->
                    <img class="items" alt="shoe"
                        src="../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png">
                    <div class="shoelabel">
                        <?php echo $shoe['productName'] ?> - <?php echo $shoe['colorName'] ?>
                    </div>
                    <a href="view.php?shoe_category=<?php echo $shoe_category ?>&shoe_type=<?php echo $shoe['productName'] ?>&shoe=<?php echo $shoe['productID'] ?>" class="form_button button">View</a>
                    
                </div>
            <?php }?>
        </div>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>