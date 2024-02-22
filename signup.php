<?php
use Aws\DynamoDb\DynamoDbClient;

require 'vendor/autoload.php'; // Include the AWS SDK for PHP
include 'inc/config.php';

require_once("inc/classes/FormSanitizer.php");
require_once('inc/classes/Constants.php'); // Include Constants
require_once('inc/classes/Account.php'); // Include Constants

if (isset($_POST["submit"])) {
    $firstName = FormSanitizer::sanitizeFormString($_POST["signup-fn"]);
    $lastName = FormSanitizer::sanitizeFormString($_POST["signup-ln"]);

    $email = FormSanitizer::sanitizeFormEmail($_POST["email"]);
    $email2 = FormSanitizer::sanitizeFormEmail($_POST["confirmEmail"]);

    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);
    $password2 = FormSanitizer::sanitizeFormPassword($_POST["confirmPassword"]);

    $account = new Account();

    $register = $account->register($firstName, $lastName, $email, $email2, $password, $password2);


    if ($register) {
        $_SESSION["userLoggedIn"] = $email;
        header("Location: index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="node_modules/video.js/dist/video-js.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>

<div class="page-signup">

<div class="signup-box">
    <p class="text-center fs-lg fw-bold">Sign Up</p>

<div class="signup-box__formWrapper">
<div class="error-message"></div>
        <form action="signup.php" method="post">
            <div class="input-group">
                <label for="signup-fn">First Name</label>
                <input type="text" name="signup-fn" id="signup-fn">
            </div>
            <div class="input-group">
                <label for="signup-ln">Last Name</label>
                <input type="text" name="signup-ln" id="signup-ln">
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
            </div>
            <div class="input-group">
                <label for="confirmEmail">Confirm Email</label>
                <input type="email" name="confirmEmail" id="confirmEmail">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
            </div>
            <div class="input-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword">
            </div>
            <div class="aic-button__container">
          <input type="submit" name="submit" value="Sign Up" class="aic-button aic-button--primary aic-button--large">
        </div>
        </form>
    </div>