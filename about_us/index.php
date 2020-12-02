<?php

//start/resume current session
session_start();

//This is a static page, it does not use local Session storage. It just displays text
//HEADER
include '../views/header.php'; ?>

<!-- MAIN BODY -->
<main>
    <div class="card">   
        
        <h3 >About Us</h3>
        <hr>
        <h4>Who are we?</h4> 
        <p>We are a shoe store that prioritizes the comfort of your feet and does not forget about style, too!
            Your feet will be happy wearing our first-class shoes!</p>

        <br>
        <h4>What can customers do on our website?</h4>
        <p>Customers can view our inventory of men’s and women’s special shoes collection.
            In addition, customers can purchase shoes, but they will need to sign in first.
            If they do not have an account, they will need to sign up before ordering their shoes.</p>
        <p>Remember to sign out when you are done!</p>
        <br>

        <h4>Where can customers view our products?</h4>           
        <p>Customers can click on the tabs for "men" and "women" on the left of the navigation bar
            to view men shoes and women shoes.</p>
        <br>

        <h4>Can customers view their previous orders?</h4>           
        <p>Yes! After customers log in to their accounts, they will see a tab on the right of the navigation bar that says
            “Purchase History”. Each customer will see list of all his/her previously ordered shoes.</p>

        <br>
        <h4>Will customers get notified when their orders are delivered?</h4>           
        <p>No, we unfortunately have not integrated this feature yet, 
            but we aim to send notifications in future versions of our website! </p>
    </div>

</main>

<!-- FOOTER-->
<?php include '../views/footer.php'; ?>