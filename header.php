<?php
include 'inc/config.php';

// Check if user is logged in. If not, redirect to Sign Up page.
if(!($_SESSION['userLoggedIn'])){
    header("Location: login.php");
}

// Set search keyword into input box, if available
if(isset($_GET['search_query'])){
    $search_placeholder = $_GET['search_query'];
} else {
    $search_placeholder = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if(isset($title)) {
        echo $title;
        } else {
            echo "American Immigration Council";
         } ?></title>
         <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./styles.css?cache=1235623212weerwrwrasddewqesdd34">
    <link rel="stylesheet" href="node_modules/video.js/dist/video-js.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&family=Karla:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
</head>
<body>
<header class="header">

    <a href="./"><img src="https://council-images.s3.us-east-2.amazonaws.com/brand-assets/logo-council.svg" class="header__logo" alt="American Immigration Council logo"></a>
<div class="searchbar">
<form action="results.php" method="GET">
    <input type="text" name="search_query" id="search_query" placeholder="Search" value="<?php echo $search_placeholder; ?>" class="searchbar__input">
    <input type="submit" id="submit" style="display:none">
    <label for="submit" class="searchbar__submit"><svg class="searchbar__searchIcon" xmlns="http://www.w3.org/2000/svg" width="29.5" height="31.089" viewBox="0 0 29.5 31.089">
  <path id="np_search_1157129_000000" d="M32.654,29,27.01,23.359a13.357,13.357,0,1,0-2.871,2.424l5.867,5.867a1.849,1.849,0,0,0,2.616,0A1.837,1.837,0,0,0,32.654,29ZM7.43,14.463a9.614,9.614,0,1,1,9.631,9.63,9.641,9.641,0,0,1-9.631-9.63Z" transform="translate(-3.7 -1.103)"/>
</svg></label>
</form>
</button>
</div>
<div class="header__rightIcons">
    <a href="./upload.php" title="Upload video"><?php include('inc/icons/upload.php'); ?></a>
    <a href="./account.php" title="Profile settings"><?php include('inc/icons/profile.php'); ?></a>
</div>

</header>