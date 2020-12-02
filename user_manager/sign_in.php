<?php
session_start();

//get parameters: email address, passsword, sign mode (sign_in/sign_up)
//$email_txt just to fill email field if we go back in case of error
$email_txt = filter_input(INPUT_POST, 'email'); 
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$input_password = filter_input(INPUT_POST, 'password');

//set the session variables to the user input so that when we go back to index.php
//if any of the variables were null, we fill out the ones that are not NULL
$_SESSION['email'] = $email_txt;
$_SESSION['input_password'] = $input_password;

//check if password is NULL, display error message if yes
if($input_password == NULL){
    $_SESSION['message'] = 'Invalid password. Please enter a valid password and try again.';
    $_SESSION['show_message'] = TRUE;
    header('Location: ../index.php');
    exit;
} 

//check if email address is null or invalid, display error message if yes
//for example, someone@factory is not valid. but someone@factory.com is valid
//FILTER_VALIDATE_EMAIL will return false if not valid email, or NULL if empty
else if($email == NULL || $email == FALSE){
    $_SESSION['message'] = 'Invalid Email Address. Please enter a valid email address and try again.';
    $_SESSION['show_message'] = TRUE;
    header('Location: ../index.php');
    exit;
}
//if the email format is valid we must validate and get the email address from the users table
//search for a user with that email address and count results to see if a record was found
else{
    require_once('../model/database.php');
    $query = "SELECT userID, firstName, lastName, password FROM user WHERE email LIKE :email";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    $result = $statement->fetchAll();
    
    //if no results returned, display error saying there is no customer with such email
    $row_count = count($result);
    if($row_count == 0){
        $statement->closeCursor();
        $_SESSION['message'] = 'We do not have customers with the email address you entered. Please enter a valid email address and try again.';
        $_SESSION['show_message'] = TRUE;
        header('Location: ../index.php');
        exit;
    }
    //if more than one result returned, display error saying there is duplicate data
    else if($row_count > 1){
        $statement->closeCursor();
        $_SESSION['message'] = 'There are more than one customers with the email address you entered. Please try again later.';
        $_SESSION['show_message'] = TRUE;
        header('Location: ../index.php');
        exit;
    }
    //if 1 results returned, validate if entered password is the same as the one in the DB
    else{
        //this will only iterate once, so there will be data for only 1 customer
        foreach ($result as $customer){

            //compare the password that the user entered with the hashed password that is in the DB
            //if the two are not the same, display error message about password
            $same_password = password_verify($input_password, $customer['password']);
            if($same_password == FALSE){
                $_SESSION['message'] = 'Your password is incorrect. Please make sure you have the correct password and try again later.';
                $_SESSION['show_message'] = TRUE;
                header('Location: ../index.php');    
                exit;
            }
            //otherwise if they are the same, welcome the user and go to index 
            // and set logged in to true, save customer's name, and 
            else{
                session_regenerate_id();
                $_SESSION['message'] = "";
                $_SESSION['show_message'] = FALSE;
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['loggedin_as'] = $customer['firstName'];
                $_SESSION['loggedin_userid'] = $customer['userID'];

                //if the user successsfully logs in, no need for email and password variables anymore
                unset($_SESSION['email']);
                unset($_SESSION['input_password']);

                //redirect to home page
                header('Location: ../index.php');
                exit;
            }
        }
    }
}
?>