<?php

//start/resume current session
session_start();

//acquire model required for query
require_once('../model/database.php');

//get parameters about shoe category, shoe type (or product name), and section
$shoe_category = filter_input(INPUT_GET, 'shoe_category');
$shoe_type = filter_input(INPUT_GET, 'shoe_type');
$section = filter_input(INPUT_GET, 'section');

///get all formal shoes 
$query = "SELECT p.productID, p.productName, replace(p.productName, ' ', '-') as productFileName, replace(c.colorName, ' ', '-') as colorName, cat.categoryName 
            from product as p, color as c, category as cat 
            where p.colorID = c.colorID and p.categoryID = :shoe_category and p.categoryID = cat.categoryID and p.`section` = :section and productName = :shoe_type
            order by p.productName, p.categoryID ";

$statement = $db->prepare($query);
$statement->bindValue(':shoe_category', $shoe_category);
$statement->bindValue(':shoe_type', $shoe_type);
$statement->bindValue(':section', $section);
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
            <a href="index.php?section=<?php echo $section?>"><?php echo ucfirst($section)?></a> > 
            <a href="category.php?section=<?php echo $section?>&shoe_category=<?php echo $shoe_category ?>">Category</a> > 
            Type 
        </h3>
        <div class="dashboard">

            <!--Iterate through array of all shoes in this type/product with different colors -->
            <?php foreach($shoes as $shoe){?>
                <div class="shoeitem">

                    <!-- To check file name :P -->
                    <!-- <p>../images/<?php echo $section?>_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png</p> -->
                    <img class="items" alt="shoe"
                        src="../images/<?php echo $section?>_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png">
                    <div class="shoelabel">
                        <?php echo $shoe['productName'] ?> - <?php echo $shoe['colorName'] ?>
                    </div>
                    <!-- Clicking on this link send parameters to view where the user can buy-->
                    <a href="view.php?section=<?php echo $section?>&shoe_category=<?php echo $shoe_category ?>&shoe_type=<?php echo $shoe['productName'] ?>&shoe=<?php echo $shoe['productID'] ?>" class="form_button button">View</a>
                    
                </div>
            <?php }?>
        </div>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>