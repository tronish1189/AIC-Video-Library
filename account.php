<?php
require 'inc/classes/Constants.php';
require 'inc/classes/DynamoDBWrapper.php';

$title = "Account - American Immigration Council";
include 'header.php';

$userEmail = $_SESSION["userLoggedIn"];

$dynamoDB = new DynamoDBWrapper(Constants::$region, Constants::$version);
$dynamoClient = $dynamoDB->getDynamoClient();

$item = $dynamoDB->getItem($dynamoClient, 'videoLibraryUsers', 'emailID', $userEmail);

$first_name = $item['Item']['first_name']['S'];
$last_name = $item['Item']['last_name']['S'];
?>
<div class="container">
    <div class="page-wrapper">
        <h1 class="page-title">Account</h1>
    <a href="logout.php">Logout</a>

    <h2>Update Account Name</h2>

    <form action="#">
    <div class="input-group">
    <label for="fn">First Name</label>
    <input type="text" name="fn" id="fn" value="<?php echo $first_name; ?>">
    </div>
    <div class="input-group">
    <label for="ln">Last Name</label>
    <input type="text" name="ln" id="ln" value="<?php echo $last_name; ?>">
    </div>
    </form>
    <form action="">
    <h2>Update Password</h2>
    <div class="input-group">
    <label for="pw">New Password</label>
    <input type="password" name="pw" id="pw">
    </div>

    <div class="input-group">
    <label for="confirmPw">Confirm New Password</label>
    <input type="password" name="confirmPw" id="confirmPw">
    </div>
    </form>
    </div>
</div>