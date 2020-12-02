<?php

//start/resume current session
session_start();

//acquire model for starting query
require_once('../model/database.php');

//get section parameter to know if we should grab women/men shoes
$section = filter_input(INPUT_GET, 'section');

///get all formal shoes 
$query = "SELECT distinct cat.categoryName, cat.categoryID, p.productName, replace(p.productName, ' ', '-') as folder
            from happyfeetshoestore.product as p, happyfeetshoestore.color as c , happyfeetshoestore.category as cat 
            where p.categoryID = cat.categoryID and p.`section` = :section
            order by cat.categoryName   ";

$statement = $db->prepare($query);
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
            <a href="<?php echo 'index.php?section=' . $section?>"><?php echo ucfirst($section) ?></a> > 
            All categories
        </h3>
        <div class="dashboard">

            <!-- Iterate through the array of shoes in this category-->
            <?php foreach($shoes as $shoe){?>
                <div class="shoeitem">

                    <!-- Show images of all shoes in this category-->
                    <img class="items" alt="shoe"
                        src="<?php echo '../images/' . $section. '_shoes/'. $shoe['categoryName'] . '/' . $shoe['folder'] .'/default.png'?>">
                                        
                    <!-- Show label of each image-->
                    <div class="shoelabel">
                        <?php echo $shoe['productName'] ?>
                    </div>

                    <!-- When user clicks on a specific product, pass parameters sbout the section, category, name to get colors of that product-->
                    <a href="<?php echo 'type.php?section='.$section.'&shoe_category='.$shoe['categoryID']. '&shoe_type='.$shoe['productName']?>" class="form_button button">View</a>
                </div>
            <?php }?>
        </div>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>