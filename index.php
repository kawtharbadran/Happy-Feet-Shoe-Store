<!--Logged in/Sign in Page-->

<?php 
session_start();

//get any variables that may have been set from the sign_in session
//such as any error messages and booleans to know whether to show them
//and variables to know if we are logged in and the users name
//also get, if set, the variables for email and password to display
if(isset($_SESSION['message']) && isset($_SESSION['show_message'])){
    $message = $_SESSION['message'];
    $show_message = $_SESSION['show_message'];
}
if(isset($_SESSION['loggedin']) && isset($_SESSION['loggedin_as'])){
    $loggedin = $_SESSION['loggedin'];
    $loggedin_as = $_SESSION['loggedin_as'];
}
if(isset($_SESSION['email']) && $_SESSION['email'] != NULL && isset($_SESSION['input_password']) && $_SESSION['input_password'] != NULL){
    $email = $_SESSION['email'];
    $input_password = $_SESSION['input_password'];
}

// HEADER
include 'views/header.php'; ?>

    <!-- MAIN BODY -->
    <main>
        <div class="card">   
            
            <?php
            //if the user has not signed in, then show div for sign in/ sign up
            //we check that user wants to sign in by checking that sign_up variable is not created yet, 
            //because it means that the user is still on the beginning page
            //or that sign_up variable is set but is false, because this means that the user has 
            //clicked on the link for signing in while he/she is on the sign_up page
            if((!isset($loggedin) || (isset($loggedin) && $loggedin == FALSE)))
            {?>
                <h3 class="sign_heading">Sign in</h3>
                <div id="sign_up_div">
                    <h5>Haven't joined yet?</h5>
                    <a href = "user_manager/sign_up.php" id="sign_up_link">Sign up</a>
                </div>
                <hr>
                <?php
                //if there was an error while signing in, show it here
                //test if message and show_message variables are valid and true,
                //this means in sign_in.php they were set when there was an error before coming here
                if(isset($message) && isset($show_message)){
                    if($message != "" && $message != NULL && $show_message == TRUE){?>
                        <p><?php echo $message; ?></p>                  
                    <?php
                    }
                }
                ?>
                <div class="table">
                    <form id="sign_in_form" action="user_manager/sign_in.php" method="post">
                        <div class="cell_small">
                            <label for="email">Email:</label>
                        </div>
                        <div class="cell_medium">
                            <input type="email" id="email" name="email"
                            <?php if(isset($email) && $email != NULL){ echo('value="'.strval($email).'"');}?>
                            required>
                        </div>
                        <br><br>
                        <div class="cell_small">
                        <label for="password">Password:</label>
                        </div>
                        <div class="cell_medium">
                            <input type="password" id="password" name="password" 
                            <?php if(isset($input_password) && $input_password != NULL){ echo('value="'.strval($input_password).'"');}?>
                            required>
                        </div>
                        <br><br>

                        <!--Hidden input to indicate if the user is signing in or up.-->
                        <!--When the user clicks sign up, JS will change the value of this input and submit the form-->
                        <!--Then sign_in page will check if it is sign up it will reload this page with sign_up instead of sign in-->
                        <input type="hidden" id="sign_mode" name="sign_mode" value="1">

                        <input type="submit" value="Sign in" class="form_button">
                    </form>
                </div>
                <?php
            }     
            //if the user has successfully signed in, just show welcome message with user's first name
            //the user will only be signed in after sucessfully matching the email and passwrd in sign_in.php
            else{?>
                <h3>Welcome, <?php echo $loggedin_as;?> !</h3>
            <?php    
            }
            ?>
        </div>

        <div class="card">
            <h3><a href="women/index.php">Shop Women Shoes as Guest</a></h3>
        </div>

        <div class="card">
            <h3><a href="men/index.php">Shop Men Shoes as Guest</a></h3>
        </div>

    </main>

    <!-- FOOTER-->
<?php include 'views/footer.php'; ?>