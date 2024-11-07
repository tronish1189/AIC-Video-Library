<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\DynamoDb\DynamoDbClient;

require 'vendor/autoload.php'; // Include the AWS SDK for PHP
require_once('inc/classes/Constants.php'); // Include Constants
require_once('inc/classes/VideoProcessing.php');

$s3client = new Aws\S3\S3Client(['region' => Constants::$region, 'version' => Constants::$version]);

$dynamoClient = new DynamoDbClient([
    // 'profile' => Constants::$profile,
    'region'  => Constants::$region,
    'version' => Constants::$version
]);

$videoID = uniqid();
$file_name = $videoID . '.mp4';
$title = $_POST['title'];
$description = $_POST['description'];
$timestamp = new DateTime();
$timestamp = $timestamp->format('c');
// $topics = '';
// $tags = '';


$videoMetaArray = array();
$videoMetaArray = array(
    'videoID'   => array('S' => $videoID),
    'title' => [
        'S' => $title,
        ],
    'titleLowercase' => [
        'S' => strtolower($title),
    ],
    'description' => [
        'S' => $description,
    ],
    'descriptionLowercase' => [
        'S' => strtolower($description),
    ],
    'timestamp' => [
        'S' => $timestamp,
    ],
    'uploaded_by' => [
        'S' => $_SESSION['userFirstName'] . ' ' . $_SESSION['userLastName'],
    ]
);

if($_POST['topics']){
$topics = array_column(json_decode($_POST['topics']), 'value');
$videoMetaArray["topics"]["SS"] = $topics;
$videoMetaArray["topicsLowercase"]["SS"] = array_map('strtolower', $topics);
}
if($_POST['locations']){
    $topics = array_column(json_decode($_POST['locations']), 'value');
    $videoMetaArray["locations"]["SS"] = $topics;
    $videoMetaArray["locations"]["SS"] = array_map('strtolower', $topics);
}
if($_POST['tags']){
    $tags = array_column(json_decode($_POST['tags']), 'value');
    $videoMetaArray["tags"]["SS"] = $tags;
    $videoMetaArray["tagsLowercase"]["SS"] = array_map('strtolower', $tags);
}

$videoFile = $_FILES['file']['tmp_name'];

$videoThumbnailPath = 'uploads/videos/thumbnails/';
$videoThumbnailPath = $videoThumbnailPath . $videoID . '-thumbnail.jpg';

$message = '';

try {
    $s3client->upload(
        Constants::$bucketName,
        $videoID . '/' . $file_name,
        fopen($videoFile, 'rb'),
        'public-read'
    );
    // $s3client->putObject([
    //     'Bucket' => Constants::$bucketName,
    //     'Key' => $videoID . '/' . $file_name,
    //     'Body' => fopen($videoFile, 'rb')
    // ]);
    $message = "Uploaded successful. See your video <a href='watch.php?v=". $videoID . "'>here</a>";
} catch (Exception $exception) {
    $message = "Failed to upload with error: " . $exception->getMessage();
    exit($message .$exception->getMessage() . " Please fix error with file upload before continuing.");
}

VideoProcessing::createVideoThumbnail($videoFile, $videoID);
VideoProcessing::uploadThumbnailToS3($videoID, $videoThumbnailPath);

$response = $dynamoClient->putItem(array(
    'TableName' => 'councilVideos',
    'Item' => $videoMetaArray
));

$title = "Video Uploaded Successfully";
include 'header.php';
?>

<div class="container">
    <p><?php echo $message; ?></p>
</div>
