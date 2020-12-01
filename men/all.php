<?php
session_start();
require_once('../model/database.php');
///get all formal shoes 
$query = "select distinct cat.categoryName, cat.categoryID, p.productName, replace(p.productName, ' ', '-') as folder
            from happyfeetshoestore.product as p, happyfeetshoestore.color as c , happyfeetshoestore.category as cat 
            where p.categoryID = cat.categoryID and p.`section` = 'men'
            order by cat.categoryName   ";
$statement = $db->prepare($query);
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
            All categories
        </h3>
        <div class="dashboard">
            <?php foreach($shoes as $shoe){?>
                <div class="shoeitem">
                    <img class="items" alt="shoe"
                        src="../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['folder'] ?>/default.png">
                    <div class="shoelabel">
                        <?php echo $shoe['productName'] ?>
                    </div>
                    <a href="type.php?shoe_category=<?php echo $shoe['categoryID'] ?>&shoe_type=<?php echo $shoe['productName'] ?>" class="form_button button">View</a>
                </div>
            <?php }?>
        </div>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>