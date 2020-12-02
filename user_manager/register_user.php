<?php
session_start();

//get parameters for adding users: email address, passsword, etc.
//email_txt just to fill email address when we go back to index.php in case of error
$first_name = filter_input(INPUT_POST, 'first_name');
$last_name = filter_input(INPUT_POST, 'last_name');
$street_address = filter_input(INPUT_POST, 'street_address');
$city = filter_input(INPUT_POST, 'city');
$US_state = filter_input(INPUT_POST, 'US_state');
$postal_code = filter_input(INPUT_POST, 'postal_code');
$country_code = filter_input(INPUT_POST, 'country_code');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$email_txt = filter_input(INPUT_POST, 'email');
$phone = filter_input(INPUT_POST, 'phone');
$input_password = filter_input(INPUT_POST, 'input_password');

//set the session variables to the user input so that when we go back to index.php
//if any of the variables were null, we fill out the ones that are not NULL
$_SESSION['first_name'] = $first_name;
$_SESSION['last_name'] = $last_name;
$_SESSION['street_address'] = $street_address;
$_SESSION['city'] = $city;
$_SESSION['US_state'] = $US_state;
$_SESSION['postal_code'] = $postal_code;
$_SESSION['country_code'] = $country_code;
$_SESSION['email'] = $email_txt;
$_SESSION['phone'] = $phone;
$_SESSION['input_password'] = $input_password;

//check if password is NULL, display error message if yes
if($input_password == NULL){
    $_SESSION['message'] = 'Invalid password. Please enter a valid password and try again.';
    $_SESSION['show_message'] = TRUE;
    header('Location: sign_up.php');
    exit;
} 

//check if email address is null or invalid, display error message if yes
//for example, someone@factory is not valid. but someone@factory.com is valid
//FILTER_VALIDATE_EMAIL will return false if not valid email, or NULL if empty
else if($email == NULL || $email == FALSE){
    $_SESSION['message'] = 'Invalid Email Address. Please enter a valid email address and try again.';
    $_SESSION['show_message'] = TRUE;
    header('Location: sign_up.php');
    exit;
}
//check if everything else that is required is NULL and display error message if yes
else if($first_name == NULL || $last_name == NULL || $street_address == NULL || $city == NULL || $US_state == NULL || $country_code == NULL || $postal_code == NULL)
{
    $_SESSION['message'] = 'Invalid Data. Please make sure all required fields have valid data and and try again.';
    $_SESSION['show_message'] = TRUE;
    header('Location: sign_up.php');
    exit;
}
//if country is US and state field is empty, display error message
else if($country_code == "US" && ($US_state == NULL || $US_state == '' || $US_state == "None")){
    $_SESSION['message'] = 'Please enter the state since the country is US (United Stated) and try again.';
    $_SESSION['show_message'] = TRUE;
    header('Location: sign_up.php');
    exit;
}
//if everything else is valid, then we must first create a new address record, then get its ID
//then encrypt the password and create a new user record with the address ID that we just created
else{
    require_once('../model/database.php');

    //if country code not "US", set state to empty string empty
    if(strtoupper($country_code) != "US"){
        $US_state = NULL;
    }
    
    //first check if the address already exists, by selecting one with the fields that were entered
    $query = "SELECT addressID FROM address
              WHERE street = :street_address
              AND   city = :city 
              AND   countryCode = :countryCode
              AND   postalCode = :postalCode";

    //if the state has been chosen, make sure we look for it too
    if($US_state != NULL){
        $query . " AND stateCode = :stateCode";
    }
    //eexecute the search statement and count the number of rows returned
    $statement = $db->prepare($query);
    $statement->bindValue(':street_address', $street_address);
    $statement->bindValue(':city', $city);
    $statement->bindValue(':countryCode', $country_code);
    $statement->bindValue(':postalCode', $postal_code);
    if($US_state != NULL){
        $statement->bindValue(':stateCode', $US_state);
    }
    $statement->execute();
    $result = $statement->fetchAll();

    //only if no same address was found, create new address
    if(count($result) == 0)
    {
        $query = "INSERT INTO address (street, city, stateCode, countryCode, postalCode)
                VALUES (:street_address, :city, :stateCode, :countryCode, :postalCode)";

        $statement = $db->prepare($query);
        $statement->bindValue(':street_address', $street_address);
        $statement->bindValue(':city', $city);
        $statement->bindValue(':stateCode', $US_state);
        $statement->bindValue(':countryCode', $country_code);
        $statement->bindValue(':postalCode', $postal_code);
        $statement->execute();

        //now get the ID of the address that was entered
        $query = "SELECT addressID FROM address  
                  WHERE street = :street_address 
                  AND   city = :city  
                  AND   countryCode = :countryCode  
                  AND   postalCode = :postalCode";
        //if the state has been chosen, make sure we look for it too
        if($US_state != NULL){
            $query = $query . " AND stateCode = :stateCode";
        }
        $statement = $db->prepare($query);
        $statement->bindValue(':street_address', $street_address);
        $statement->bindValue(':city', $city);
        if($US_state != NULL){
            $statement->bindValue(':stateCode', $US_state);
        }        
        $statement->bindValue(':countryCode', $country_code);
        $statement->bindValue(':postalCode', $postal_code);
        $statement->execute();
        $result = $statement->fetchAll();
    }

    //make sure we got something back by counting results and ensuring it is one result
    $row_count = count($result);
    if($row_count == 1){

        //will only iterate once for the address we found
        foreach ($result as $address){

            //if no phone was entered, enter NULL
            $phone = ($phone == NULL || $phone = "")? NULL: $phone;

            //now encrypt password and insert it along with other information and addressID
            $encrypted_password = password_hash($input_password, PASSWORD_DEFAULT);

            //use ignore keyword to prevent duplicates. if the same user already exists, 
            //the query will be ignored without causing an error, and result status will still be successful (OK)
            $query = "INSERT IGNORE INTO user (firstName, lastName, email, phone, addressID, password)
                    VALUES (:first_name, :last_name, :email, :phone, :addressID, :password)";
                    
            $statement = $db->prepare($query);
            $statement->bindValue(':first_name', $first_name);
            $statement->bindValue(':last_name', $last_name);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':phone', $phone);
            $statement->bindValue(':addressID', $address['addressID']);
            $statement->bindValue(':password', $encrypted_password);
            $success = $statement->execute();

            //get user to check it has been added
            $query = "SELECT userID FROM user 
                      WHERE email = :email AND password = :password";
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':password', $encrypted_password);
            $statement->execute();
            $result = $statement->fetchAll();

            //if query was successful, log the user and go to index.php to welcome new user
            if(count($result) > 0){
                $_SESSION['message'] = "";
                $_SESSION['show_message'] = FALSE;
                $loggedin = TRUE;
                $loggedin_as = $first_name;
                header('Location: ../index.php');
                exit;
            }
            //if no users found then query was not successful
            else{
                $_SESSION['message'] = 'There was an error while creating your account. Please try again later.';
                $_SESSION['show_message'] = TRUE;
                header('Location: sign_up.php');
                exit;
            }
        }
    }
    //if we got no addresses back or more than 1 when getting addressID, display an error
    else{
        $_SESSION['message'] = 'There was an error while saving your address. Please try again later.';
        $_SESSION['show_message'] = TRUE;
        header('Location: sign_up.php');
        exit;
    }
}
?>