<?php

//start/resume current session
session_start();

//This page will just pull all previous orders for the user that is signed in 
//by searching for all orders in shoorder table that have the userID that is stored in the local Session storage

//check if the user is logged in to get their name and to look up their purchases
if(isset($_SESSION['loggedin']) && isset($_SESSION['loggedin_as'])){
    $loggedin = $_SESSION['loggedin'];
    $loggedin_as = $_SESSION['loggedin_as'];
    $loggedin_userid = $_SESSION['loggedin_userid'];
}

//if logged in user id is valid, get all products purchased for that user from shoeorder table
if($loggedin_userid != NULL){
    require_once('../model/database.php');

    $query = "SELECT DISTINCT s.productID as 'productID',
                            p.productName as 'shoeName',
                            p.price as 'price',
                            col.colorName as 'color', 
                            p.section as 'section', 
                            s.date as 'date', 
                            CONCAT(a.street, ', <br>', a.city, ', <br>', COALESCE(a.stateCode,''), ' ', a.postalCode, ', ', a.countryCode) as 'address', LOWER(REPLACE(productName, ' ', '-')) as productFolderName, 
                            CONCAT(LOWER(REPLACE(productName, ' ', '-')), '-',(REPLACE(col.colorName, ' ', '-'))) as productFileName, 
                            cat.categoryName as 'categoryName',
                            cat.categoryID as 'categoryID'
            FROM `shoeorder` as s 
            INNER JOIN (`product` as p , `category` as cat, `color` as col, `address` as a) 
            ON s.productID = p.productID AND p.categoryID = cat.categoryID AND p.colorID = col.colorID AND s.addressID = a.addressID AND s.userID = :logged_in_userID;";

    $statement = $db->prepare($query);
    $statement->bindValue(':logged_in_userID', $loggedin_userid);
    $statement->execute();
    $previous_orders = $statement->fetchAll();
    $statement->closeCursor();
}

//HEADER
include '../views/header.php';?>


<!-- MAIN BODY -->
<main>
    <div class="card wide"> 
        <?php //if logged in user ID was null, display error message
            if($loggedin_userid == NULL){?>
                <h4>Oops, there was a problem loading your purchase history. Please try again later.</h4>
            <?php
            }
            //otherwise check if we got any results back from the query for searching for products.
            //if we got nothing back, it means the user has no previous purchases, so just display a message
            else if(count($previous_orders) == 0){?>
                <h4>You have not purchased anything yet from our store!</h4>
            <?php
            }
            //but if we did get something back, display the details in a table
            else if(count($previous_orders) > 0){?>
                <h3>Your Previous Orders</h3>
                <br>
                <table class="history_table">
                    <tr>
                        <th>Purchased Shoes</th>
                        <th>Category</th>
                        <th>Color</th>
                        <th>Section</th>
                        <th>Price</th>
                        <th>Date Purchased</th>
                        <th>Ship to Address</th>
                        <th>&nbsp;</th>
                    <tr>
                    <?php
                    //iterate through array of previosly ordered shoes to fill the table
                    foreach($previous_orders as $shoes){?>
                        <tr>
                            <td><?php echo $shoes['shoeName'] ?></td>
                            <td><?php echo $shoes['categoryName'] ?></td>
                            <td><?php echo $shoes['color'] ?></td>
                            <td><?php echo $shoes['section'] ?></td>
                            <td><?php echo '$'.$shoes['price'] ?></td>
                            <td><?php echo $shoes['date'] ?></td>
                            <td><?php echo $shoes['address'] ?></td>
                            
                            <!-- When the user clicks the view button or link, he/she will be redirected to the view page of that product-->
                            <!-- The parameters passed to the view page, shoe name/type, category ID, and section are brought from the SQL query -->
                            <td><a href="<?php echo '../section/view.php?section='.$shoes['section']. '&shoe_category='. $shoes['categoryID'].'&shoe_type='. $shoes['shoeName']. '&shoe='. $shoes['productID']?>" class="form_button">View in Inventory</a></td>
                        <tr>
                    <?php
                    }
                    ?>
                </table>
            <?php
            }
        ?>  
    </div>
</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>