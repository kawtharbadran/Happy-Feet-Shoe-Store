<?php
session_start();
require_once('../model/database.php');
//get all shoes category
$query = "select concat(upper(left(categoryName,1)),substring(categoryName,2,length(categoryName))) as categoryName , categoryID , replace(productName, ' ', '-') as foldername from
            (select distinct a.categoryName, a.categoryID , productName, ROW_NUMBER() OVER (PARTITION BY categoryName)  as RowNumber from
            (select distinct c.categoryName, c.categoryID , p.productName, ROW_NUMBER() OVER (PARTITION BY productName)  as RowNumber 
            from happyfeetshoestore.category as c, happyfeetshoestore.product as p
            where p.categoryID = c.categoryID and p.`section` = 'men') as a
            where a.RowNumber = 1) as b
            where b.RowNumber = 1   ";
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
                Men 
            </h3>

            <div class="dashboard">

                <?php foreach($shoes as $shoe){?>
                    <div class="shoeitem">
                        <img class="items" alt="shoe"
                            src="../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['foldername'] ?>/default.png">
                        <div class="shoelabel">
                            <?php echo $shoe['categoryName'] ?>
                        </div>
                        <a href="category.php?shoe_category=<?php echo $shoe['categoryID'] ?>" class="form_button button">View</a>
                    </div>
                <?php }?>

            </div>

        <br><br>
        <a href="./all.php" type="button" class="form_button button">Show All Men Shoes</a>
    </div>

</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>