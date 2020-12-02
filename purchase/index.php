<?php

//start/resume current session
session_start();

// HEADER
include '../views/header.php';

//check if the user is logged in to get their name ans ID that will be used to purchase
//because when a purchase is places, a new record is entered in the shoeorder table
if(isset($_SESSION['loggedin']) && isset($_SESSION['loggedin_as'])){
    $loggedin = $_SESSION['loggedin'];
    $loggedin_as = $_SESSION['loggedin_as'];
    $loggedin_userid = $_SESSION['loggedin_userid'];
}

//get shoeID from the form that was posted which is the product ID to enter in the shoeorder table
$shoeID = filter_input(INPUT_POST, 'shoeID');

//get section which is wither "women" or "men"
$section = filter_input(INPUT_POST, 'section');

//if shoeID is null try to get it again from get instead of post
if ($shoeID === NULL) {
    $shoeID = filter_input(INPUT_GET, 'shoeID');
}
//if section is null try to get it again from get instead of post
if ($section === NULL) {
    $section = filter_input(INPUT_GET, 'section');
}

//if any of the parameters shoeID andn section are NULL, then there is an error, 
//and the purchase cannot be made, so display the error
if($shoeID == NULL || $section == NULL) {
    $error = 'Invalid Payload. Please make the purchase again.';
?>

<!--And let the user know that the order was not made-->
<main>
    <div class="dashboard">
        <div class="card">
           <h4>Your order was NOT placed</h4> 
           <h6><?php echo $error ?></h6>
           <a href="../index.php" class="form_button">Back to main page</a> 
        </div>
    </div>
</main>

<?php    
} 
//otherwise if the parameters are both valid (NOT NULL), create a new shoeorder record by first getting the address
//get the address by searching for it in the user table using the userID
else {
    require_once('../model/database.php');

    $query = "SELECT addressID FROM happyfeetshoestore.`user` as u where u.userID = :userID  ";
    $statement = $db->prepare($query);
    $statement->bindValue(':userID', $loggedin_userid);
    $statement->execute();
    $addressID = $statement->fetch();
    $statement->closeCursor();
?>

<!--Execute the script below to print addressID and userID to console for debugging purposes-->
<script>
    console.log(<?php echo $loggedin_userid ?>);
    console.log(<?php echo $addressID['addressID'] ?>);
</script>

<?php
//now insert the shoeorder record with the userID that was saved in the SESSION local storage
//shoeID that is the productID that we got from the posted shoeID, now() to get current DATE in SQL, and addressID that we looked up
$query = "INSERT INTO shoeorder VALUES (:userID, :shoeID, now(), :addressID)   ";
$statement = $db->prepare($query);
$statement->bindValue(':userID', $loggedin_userid);
$statement->bindValue(':shoeID', $shoeID);
$statement->bindValue(':addressID', $addressID['addressID']);
$statement->execute();
$statement->closeCursor();

//now get the product that the user just ordered to display details about it
$query = "SELECT p.productID, p.productName, p.price, replace(p.productName, ' ', '-') as productFileName, replace(c.colorName, ' ', '-') as colorName, cat.categoryName 
            FROM product as p, color as c, category as cat 
            WHERE p.colorID = c.colorID and p.categoryID = cat.categoryID and productID = :shoe and section = :section
            ORDER BY p.productName, p.categoryID ";

//bind productID parameter to shoeID value and 
//bind section value to section variable that indicates if "men"/"women", was submitted as hidden input in html form        
$statement = $db->prepare($query);
$statement->bindValue(':shoe', $shoeID);
$statement->bindValue(':section', $section);
$statement->execute();
$shoe = $statement->fetch();
$statement->closeCursor();

//now get the order date in which the order was placed in the shoeOrder table 
$query = "SELECT `date` as orderplaced, date_add(`date`, interval 14 day) as deliverydate, firstName, lastName, street, city, stateCode, countryCode
            FROM shoeorder as s, address as a, `user` as u
            WHERE u.addressID = a.addressID and s.userID = u.userID and u.userID = :userID and s.productID = :shoeID";
$statement = $db->prepare($query);
$statement->bindValue(':shoeID', $shoeID);
$statement->bindValue(':userID', $loggedin_userid);
$statement->execute();
$order = $statement->fetch();
$statement->closeCursor();
?>

<!-- Display details about the order that the user placed-->
<main>
    <div class="card">
        <div class="dashboard"> 
           <h4>Your order placed successfully</h4>
            <h6>Here are the details</h6>
           <div class="side">
                <div class="pic">
                    <div class="panel">
                        <img alt="shoe" style="height:256px; width:256px;" 
                        <?php
                        if($section=="men"){  ?>
                            src="../images/men_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png">
                        <?php
                        }
                        else if($section == "women"){?>
                            src="../images/women_shoes/<?php echo $shoe['categoryName'] ?>/<?php echo $shoe['productFileName'] ?>/<?php echo $shoe['productFileName'] ?>-<?php echo $shoe['colorName'] ?>.png">
                        <?php
                        }?>
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
        </div>

        <!--Display Mock Tracking history details-->
        <h2>Tracking History</h2>
        <table class="tracking">
            <div>
                <h4>Placed by:</h4> <?php echo $order['firstName'] ?> <?php echo $order['lastName'] ?>
                <h4>Originated:</h4> Riyadh, Saudi Arabia
                <h4>Drop-off:</h4> <?php echo $order['street'] ?> <?php echo $order['city'] ?> <?php echo $order['stateCode'] ?> <?php echo $order['countryCode'] ?>
                <h4>Tracking Number:</h4> A0123456789
                <h4>Order Placed:</h4> <?php echo $order['orderplaced'] ?>
                <h4>Est. Delivery:</h4> <?php echo $order['deliverydate'] ?>
            </div>
            <br><br>
            <tr>
                <td>
                    <img style="height:96px; width:96px" src="../images/placed.png">
                </td>
                <td>
                    <img style="height:96px; width:96px" src="../images/processing.png">
                </td>
                <td>
                    <img style="height:96px; width:96px" src="../images/outfordelivery.png">
                </td>
                <td>
                    <img style="height:96px; width:96px" src="../images/delivered.png">
                </td>
            </tr>
            <tr>
                <td>
                    <i class="fa fa-check-circle"></i> Ordered
                </td>
                <td>
                    <i class="fa fa-check-circle"></i> Dispatched
                </td>
                <td style="color: orange;">
                    <i class="fa fa-clock"></i> Out for Delivery
                </td>
                <td style="color: orange;">
                    <i class="fa fa-clock"></i> Delivered
                </td>
            </tr>
        </table>
        <br><br><br>
        <a href="../index.php" class="form_button">Shop more</a>  
    </div>
</main>

<?php }?>
<!-- FOOTER-->
<?php include '../views/footer.php'; ?>