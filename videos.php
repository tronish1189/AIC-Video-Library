<?php
use Aws\Exception\AwsException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;

require_once('inc/classes/Constants.php'); // Include Constants
require 'vendor/autoload.php'; // Include the AWS SDK for PHP

include 'header.php';

// echo   $_SESSION["userLoggedIn"];
// echo $_SESSION["userFirstName"];
// echo  $_SESSION["userLastName"];

$videosPerPage = 21;

$s3client = new Aws\S3\S3Client(['region' => Constants::$region, 'version' => Constants::$version]);

$dynamoClient = new DynamoDbClient([
    // 'profile' => Constants::$profile,
    'region'  => Constants::$region,
    'version' => Constants::$version
]);

$iterator = $dynamoClient->scan(array(
    'TableName' => 'councilVideos',
    'Limit' => $videosPerPage
));
$items = $iterator['Items'];
?>

<div class="container">
    <h1 class="page-title">All Videos</h1>
    <div class="recentVideos">
<?php
foreach ($items as $item) {

    echo '<div class="videoCard"><a href="./watch.php?v=' . $item['videoID']['S'] . '">';

    $cmd = $s3client->getCommand('GetObject', [
        'Bucket' => Constants::$bucketName,
        'Key' => $item['videoID']['S'] . '/' . $item['videoID']['S'] . '-thumbnail.jpg'
        ]
    );

    $request = $s3client->createPresignedRequest($cmd, '+20 minutes');

    // Get the actual presigned-url
    $presignedUrl = (string)$request->getUri();

    echo '<img class="videoCard__thumbnail" src="' . $presignedUrl . '">';

    echo '</a><a class="videoCard__title" href="./watch.php?v=' . $item['videoID']['S'] . '">' . $item['title']['S'] . '</a>';

    if(array_key_exists("tags", $item)){
        echo '<span class="videoCard__tags">';
        $tags = implode(', ', $item['tags']['SS']);
        echo $tags;
        echo '</span>';
    }
    echo '</div>';
    }
?>

</div>
<?php

$numVideos = count($items);

if($numVideos >= $videosPerPage){
?>
<a href="#">See all Videos</a>
<?php
}
?>
</div>