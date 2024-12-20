<?php
use Aws\DynamoDb\DynamoDbClient;

require_once("inc/config.php");
require_once("inc/classes/FormSanitizer.php");
require_once("inc/classes/Constants.php");
require_once("inc/classes/Account.php");

require 'vendor/autoload.php'; // Include the AWS SDK for PHP

$account = new Account();

if (isset($_POST["submit"])) {

    $email = FormSanitizer::sanitizeFormUsername($_POST["email"]);
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);

    $wasSuccessful = $account->login($email, $password);
    if ($wasSuccessful) {

        $dynamoClient = new DynamoDbClient([
            // 'profile' => Constants::$profile,
            'region' => Constants::$region,
            'version' => Constants::$version,
            'credentials' => [
                'key' => Constants::$accessKey,
                'secret' => Constants::$secretKey,
            ],
        ]);

        $userItem = $dynamoClient->getItem(array(
            'ConsistentRead' => true,
            'TableName' => 'videoLibraryUsers',
            'Key' => array(
                'emailID' => array('S' => $email),
            )
        ));

        $_SESSION["userLoggedIn"] = $email;
        $_SESSION["userFirstName"] = $userItem['Item']['first_name']['S'];
        $_SESSION["userLastName"] = $userItem['Item']['last_name']['S'];

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
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="page-login">

        <div class="login-box">
            <p class="text-center fs-lg fw-bold">Login</p>

            <div class="login-box__formWrapper">
                <form action="login.php" method="POST">
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="text" name="email" id="email">
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password">
                    </div>
                    <div class="aic-button__container">
                        <input type="submit" name="submit" value="Login"
                            class="aic-button aic-button--primary aic-button--large">
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>