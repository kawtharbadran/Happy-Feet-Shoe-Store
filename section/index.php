<?php

//start/resume current session
session_start();

//get section parameter to kow if we are buying for men or women
$section = filter_input(INPUT_GET, 'section');

//require file for model database to start query
require_once('../model/database.php');

//if section was not null, get all shoes category for the specific section
if($section != NULL){
    $query = "SELECT concat(upper(left(categoryName,1)),substring(categoryName,2,length(categoryName))) as categoryName , categoryID , replace(productName, ' ', '-') as foldername from
                (select distinct a.categoryName, a.categoryID , productName, ROW_NUMBER() OVER (PARTITION BY categoryName)  as RowNumber from
                (select distinct c.categoryName, c.categoryID , p.productName, ROW_NUMBER() OVER (PARTITION BY productName)  as RowNumber 
                from happyfeetshoestore.category as c, happyfeetshoestore.product as p
                where p.categoryID = c.categoryID and p.`section` = :section) as a
                where a.RowNumber = 1) as b
                where b.RowNumber = 1   ";
    $statement = $db->prepare($query);
    $statement->bindValue(':section', $section);
    $statement->execute();
    $shoes = $statement->fetchAll();
    $statement->closeCursor();
}

// HEADER
include '../views/header.php';
?>

<main>
    
    <div class="card">
        <?php 
        //display an error message if the section was NULL since user won't be able to know if men/women section
        //This should never happen
        if($section == NULL){?>
            <h3>There was an error loading this section. Please try again later.</h3>
        <?php 
        }
        //otherwise if the section was valid, display section and inventory images
        else {?>

            <h3>
                <!-- Display men/women section-->
                <a href="../index.php">Home</a> > 
                <?php echo ucfirst($section)?> 
            </h3>

            <div class="dashboard">
                <!-- Display available categories: slippers/formal/casual/boots -->
                <?php foreach($shoes as $shoe){?>
                    <div class="shoeitem">
                        <img class="items" alt="shoe"
                            src="<?php echo '../images/' . $section .'_shoes/' . $shoe['categoryName'] .'/' . $shoe['foldername'] .'/default.png'?>">
                        <div class="shoelabel">
                            <?php echo $shoe['categoryName'] ?>
                        </div>
                        <!-- when a user clicks to view a certain category, pass section parameter to know to get shoes for men/women-->
                        <a href="<?php echo 'category.php?section=' .$section. '&shoe_category=' . $shoe['categoryID'] ?>" class="form_button button">View</a>
                    </div>
                <?php }?>

            </div>

        <br><br>
        <!-- If user clicks on show all shoes, pass section as a parameter to know whether ot get women or men shoes-->
        <a href="<?php echo './all.php?section=' .$section?>" type="button" class="form_button button">Show All <?php echo ucfirst($section)?> Shoes</a>
        <?php
        }?>
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>