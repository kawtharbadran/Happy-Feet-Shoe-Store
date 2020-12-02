<?php 
    //define part of the paths that are in a deeper level in the working directory
    //below in the html, we will test to see if user is at one of these pages 
    //so we can correctly reference the CSS sheet and nav bar links
    $window = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $women_section = "/women/";
    $men_section = "/men/";
    $purchase_section = "/purchase/";
    $sign_in_section = "/user_manager/"
?>

<!DOCTYPE html>
<html lang="en">
	<head>
        <title> Happy Feet Shoe Store</title>
        <meta charset="utf-8">
        <?php 
        //testing current window so we can correctly reference CSS sheet
        if(strpos($window, $purchase_section) || 
            strpos($window, $women_section) || 
           strpos($window, $men_section) ||
           strpos($window, $sign_in_section)){ ?>
            <link rel="stylesheet" type="text/css" href="../styles/main.css">
            <link rel="stylesheet" href="../styles/normalize.css" type="text/css">
        <?php 
        }
        else{ ?>
            <link rel="stylesheet" type="text/css" href="styles/main.css">
            <link rel="stylesheet" href="styles/normalize.css" type="text/css">
        <?php 
        }
    ?>
        <!-- This will be displayed regardless of where the current window is -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>

    <body>

	<!-- HEADER SECTION -->
	<header>

        <!--HEADING BAR-->
        <div class="header-bar">
            <h1> Happy Feet Shoe Store</h1>
            <i class="fas fa-shoe-prints fa-rotate-270"></i>
        </div>

        <!-- NAV BAR -->
        <ul class="nav-list">

        <?php if(strpos($window, $purchase_section) || 
                strpos($window, $women_section) || 
                strpos($window, $men_section) ||
                strpos($window, $sign_in_section)) {
        ?>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../men">Men</a></li>
            <li><a href="../women">Women</a></li>
            <li class="right_nav_tab"><a href="../aboutus">About us</a></li>
            <li class="right_nav_tab"><a href="../history">Purchase History</a></li>
        <?php
        } else {
        ?>
            <li><a href="./index.php">Home</a></li>
            <li><a href="./men">Men</a></li>
            <li><a href="./women">Women</a></li>
            <li class="right_nav_tab"><a href="./aboutus">About us</a></li>
        <?php
        }
        ?>
            <?php  
            //if the user is logged in and has a valid session, display sign out tab in nav bar 
            if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE)
            {
                //check which window we are on to correctly reference sign_out.php
                if(strpos($window, $purchase_section) || 
                    strpos($window, $women_section) || 
                    strpos($window, $men_section) ||
                    strpos($window, $sign_in_section))
                {?>
                    <li class="right_nav_tab"><a href="./history">Purchase History</a></li>
                    <li class="right_nav_tab"><a href="../user_manager/sign_out.php">Sign out</a></li>
                <?php
                }
                else
                {?>
                    <li class="right_nav_tab"><a href="./history">Purchase History</a></li>
                    <li class="right_nav_tab"><a href="user_manager/sign_out.php">Sign out</a></li>
                <?php
                }
            }
            ?>
        </ul>
    </header>

