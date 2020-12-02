<?php
session_start();
// HEADER
include '../views/header.php';

if(isset($_SESSION['loggedin']) && isset($_SESSION['loggedin_as'])){
    $loggedin = $_SESSION['loggedin'];
    $loggedin_as = $_SESSION['loggedin_as'];
    $loggedin_userid = $_SESSION['loggedin_userid'];
}

$shoeID = filter_input(INPUT_POST, 'shoeID');
if ($shoeID === NULL) {
    $shoeID = filter_input(INPUT_GET, 'shoeID');
}
if($shoeID == NULL) {
    $error = 'Invalid Payload. Please make the purchase again.';
?>

<main>
    <div class="dashboard">
        <div class="card">
           <h4>Your order did NOT placed</h4> 
           <h6><?php echo $error ?></h6>
           <a href="../index.php" class="form_button">Back to main page</a> 
        </div>
    </div>
</main>

<?php    } else {
require_once('../model/database.php');

$query = "SELECT addressID FROM happyfeetshoestore.`user` as u where u.userID = :userID  ";
$statement = $db->prepare($query);
$statement->bindValue(':userID', $loggedin_userid);
$statement->execute();
$addressID = $statement->fetch();
$statement->closeCursor();

?>

<script>
    console.log(<?php echo $loggedin_userid ?>);
    console.log(<?php echo $addressID['addressID'] ?>);
    </script>

<?php

$query = "insert into shoeorder values (:userID, :shoeID, now(), :addressID)   ";
$statement = $db->prepare($query);
$statement->bindValue(':userID', $loggedin_userid);
$statement->bindValue(':shoeID', $shoeID);
$statement->bindValue(':addressID', $addressID['addressID']);
$statement->execute();
$statement->closeCursor();

$query = "select p.productID, p.productName, p.price, replace(p.productName, ' ', '-') as productFileName, replace(c.colorName, ' ', '-') as colorName, cat.categoryName 
            from product as p, color as c, category as cat 
            where p.colorID = c.colorID and p.categoryID = cat.categoryID and productID = :shoe
            order by p.productName, p.categoryID ";
$statement = $db->prepare($query);
$statement->bindValue(':shoe', $shoeID);
$statement->execute();
$shoe = $statement->fetch();
$statement->closeCursor();

$query = "select `date` as orderplaced, date_add(`date`, interval 14 day) as deliverydate, firstName, lastName, street, city, stateCode, countryCode
            from shoeorder as s, address as a, `user` as u
            where u.addressID = a.addressID and s.userID = u.userID and u.userID = :userID and s.productID = :shoeID";
$statement = $db->prepare($query);
$statement->bindValue(':shoeID', $shoeID);
$statement->bindValue(':userID', $loggedin_userid);
$statement->execute();
$order = $statement->fetch();
$statement->closeCursor();
?>

<main>
    <div class="card">
        <div class="dashboard"> 
           <h4>Your order placed successfully</h4>
            <h6>Here are the details</h6>
           <div class="side">
                <div class="pic">
                    <div class="panel">
                        <img alt="shoe" style="height:256px; width:256px;"
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

        </div>
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