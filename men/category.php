<?php
session_start();
require_once('../model/database.php');
$shoe_category = filter_input(INPUT_GET, 'shoe_category');
//get all shoes based on category 
$query = "select distinct cat.categoryName, p.productName, replace(p.productName, ' ', '-') as productFileName, replace(p.productName, ' ', '-') as folder
            from happyfeetshoestore.product as p, happyfeetshoestore.color as c , happyfeetshoestore.category as cat 
            where p.categoryID = cat.categoryID and p.`section` = 'men' and cat.categoryID = :shoe_category
            order by cat.categoryName   ";
$statement = $db->prepare($query);
$statement->bindValue(':shoe_category', $shoe_category);
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
            Category
        </h3>
        <div class="dashboard">
            <?php foreach($shoes as $shoe){?>
                <div class="shoeitem">
                    <img class="items" alt="shoe"
                        src="../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/default.png">
                    <div class="shoelabel">
                        <?php echo $shoe['productName'] ?>
                    </div>
                    <a href="type.php?shoe_category=<?php echo $shoe_category ?>&shoe_type=<?php echo $shoe['productName'] ?>" class="form_button button">View</a>
                    <!-- <a href="<?php echo $shoe['productName'] ?>.php" class="form_button button">View</a> -->
                </div>
            <?php }?>
        </div>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>