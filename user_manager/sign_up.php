<?php
session_start();

//SIGN UP FORM
//get all countries to populate in country drop-down menu
require_once('../model/database.php');
$query = "SELECT * FROM country";
$statement = $db->prepare($query);
$statement->execute();
$countries = $statement->fetchAll();
$statement->closeCursor();

///get all states to populate states in drop-down menu
$query = "SELECT * FROM state";
$statement = $db->prepare($query);
$statement->execute();
$states = $statement->fetchAll();
$statement->closeCursor();

//make sure we have results back, display error message if not
if(count($countries) == 0 || count($states) == 0){
    $message = "There was an error loading the sign up form. Please try again later";
    $show_message = TRUE;
}
//get any variables that may have been set from the sign_in session
//such as any error messages and booleans to know whether to show them
if(isset($_SESSION['message']) && isset($_SESSION['show_message'])){
    $message = $_SESSION['message'];
    $show_message = $_SESSION['show_message'];
}
//if the user still has not logged in and clicked on sign up,
//then display the div for signing up instead of signing in
include '../views/header.php';
?>

    <main>
        <div class="card">   
            <h3 class="sign_heading">Sign up</h3>
            <div id="sign_in_div">
                <h5>Already have an account?</h5>
                <a id="sign_in_link" href = "../index.php">Sign in</a>
            </div>
            <hr>
            <?php
            //if there was an error while signing up, show it here
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

                <!--SIGN UP FORM -->
                <form id="sign_up_form" action="register_user.php" method="post">
                                    
                    <!-- FIRST NAME -->
                    <div class="cell_small">
                        <label for="first_name">First name:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="text" id="first_name" placeholder="e.g. William" name="first_name"
                        <?php if(isset($_SESSION['first_name']) && $_SESSION['first_name'] != NULL){ echo('value="'.$_SESSION['first_name'].'"');}?>
                        required>

                    </div>
                    <br><br>
                    <!-- LAST NAME -->
                    <div class="cell_small">
                        <label for="last_name">Last name:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="text" id="last_name" placeholder="e.g. Smith" name="last_name" 
                        <?php if(isset($_SESSION['last_name']) && $_SESSION['last_name'] != NULL){ echo('value="'.$_SESSION['last_name'].'"');}?>
                        required>
                    </div>
                    <br><br>

                    <!-- EMAIL ADDRESS -->
                    <div class="cell_small">
                        <label for="email">Email:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="email" id="email" name="email" placeholder="e.g. willy@domain.com" 
                        <?php if(isset($_SESSION['email']) && $_SESSION['email'] != NULL){ echo('value="'.$_SESSION['email'].'"');}?>
                        required>
                    </div>
                    <br><br>

                    <!-- PHONE -->
                    <div class="cell_small">
                        <label for="phone">Phone (optional):</label>
                    </div>
                    <div class="cell_medium">
                        <input type="text" id="phone" name="phone" placeholder="e.g. (111) 222-3333" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}"
                        <?php if(isset($_SESSION['phone']) && $_SESSION['phone'] != NULL){ echo('value="'.$_SESSION['phone'].'"');}?>>
                        <p>Format:(XXX) XXX-XXXX</p>
                    </div>
                    <br><br>

                    <!-- STREET ADDRESS -->
                    <div class="cell_small">
                        <label for="street_address">Street Address:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="text" name="street_address" placeholder="e.g. 49 Tree St, E 400" 
                        <?php if(isset($_SESSION['street_address']) && $_SESSION['street_address'] != NULL){ echo('value="'.$_SESSION['street_address'].'"');}?>
                        required>
                    </div>
                    <br><br>

                    <!-- CITY -->
                    <div class="cell_small">
                        <label for="city">City:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="text" name="city" placeholder="e.g. Boston" 
                        <?php if(isset($_SESSION['city']) && $_SESSION['city'] != NULL){ echo('value="'.$_SESSION['city'].'"');}?>
                    required>
                    </div>
                    <br><br>

                    <!-- STATE -->
                    <!-- We will not require this because some countries don't have states, but after submitting form we will check if is filled if the country is US -->
                    <div class="cell_small">
                        <label for="state">State:</label>
                    </div>
                    <div class="cell_medium">
                        <select id="state_code_select" name="US_state">
                            <option value="None">None</option>
                            <?php foreach($states as $_state){?>
                                <option value="<?php echo $_state['stateCode']?>">
                                    <?php echo $_state['stateCode'].' - '. $_state['stateName'];?>
                                </option>
                            <?php
                            }?>
                        </select>                        
                    </div>
                    <br><br>

                    <!-- POSTAL CODE -->
                    <div class="cell_small">
                        <label for="postal_code">Postal Code:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="text" name="postal_code" placeholder="e.g. 02134" 
                        <?php if(isset($_SESSION['postal_code']) && $_SESSION['postal_code'] != NULL){ echo('value="'.$_SESSION['postal_code'].'"');}?>
                        required>
                    </div>
                    <br><br>

                    <!-- COUNTRY CODE -->
                    <div class="cell_small">
                        <label for="country_code">Country Code:</label>
                    </div>
                    <div class="cell_medium">
                        <select id="country_code_select" name="country_code" required>
                            <?php foreach($countries as $country){?>
                                <option value="<?php echo $country['countryCode']?>">
                                    <?php echo $country['countryCode'].' - '. $country['countryName'];?>
                                </option>
                            <?php
                            }?>
                        </select>      
                    </div>
                    <br><br>

                    <!-- PASSWORD -->
                    <div class="cell_small">
                        <label for="password">Password:</label>
                    </div>
                    <div class="cell_medium">
                        <input type="password" id="input_password" name="input_password"
                        <?php if(isset($_SESSION['input_password'] ) && $_SESSION['input_password']  != NULL){ echo('value="'.$_SESSION['input_password'] .'"');}?>
                        required>
                    </div>
                    <br><br>
                    <input type="submit" value="Sign up" class="form_button">
                </form>
            </div>
        </div>
    </main>
<?php include '../views/footer.php';?>

    <script src="../scripts/select_option.js"></script>
<?php
    
    //if the country_code was set, select it by calling function to select to keep previous input
    if(isset($_SESSION['country_code']) && $_SESSION['country_code'] != NULL){?>
        <script type="text/javascript">
            select_country('<?php echo strval($_SESSION['country_code'])?>');
        </script>
        <?php
    }
    //if the state were set, select it by calling function to select to keep previous input
    if(isset($_SESSION['US_state']) && $_SESSION['US_state'] != NULL){?>
        <script type="text/javascript">
            select_state('<?php echo strval($_SESSION['US_state'])?>');
        </script>
        <?php
    }
?>