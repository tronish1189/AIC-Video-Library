<?php

use Aws\Exception\AwsException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\S3\S3Client;

require_once('inc/classes/Constants.php'); // Include Constants
require_once('inc/classes/Functions.php');
require 'vendor/autoload.php'; // Include the AWS SDK for PHP

include 'header.php';

$videosPerPage = 9;

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

// Sort videos by recently uploaded
usort($items, function ($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
});

$scanTags = $dynamoClient->scan(array(
    'TableName' => 'councilVideos',
    'ProjectionExpression' => 'tags'
));

$scanTopics = $dynamoClient->scan(array(
    'TableName' => 'councilVideos',
    'ProjectionExpression' => 'topics'
));

$popularContentArray = [];

$tagsList = [];
$tagsList = Functions::pushMetaToArray($scanTags, "tags", $tagsList);

$counted = array_count_values($tagsList);
arsort($counted); //sort descending maintain keys
$most_frequent = key($counted); //get the key, as we are rewound it's the first key
next($counted);
$most_frequent2 = key($counted); //get the key, as we are rewound it's the first key
next($counted);
$most_frequent3 = key($counted); //get the key, as we are rewound it's the first key
array_push($popularContentArray, $most_frequent);
array_push($popularContentArray, $most_frequent2);
array_push($popularContentArray, $most_frequent3);

$topicsList = [];
$topicsList = Functions::pushMetaToArray($scanTopics, "topics", $topicsList);

$counted = array_count_values($topicsList);
arsort($counted); //sort descending maintain keys
$most_frequent = key($counted); //get the key, as we are rewound it's the first key
next($counted);
$most_frequent2 = key($counted); //get the key, as we are rewound it's the first key
next($counted);
$most_frequent3 = key($counted); //get the key, as we are rewound it's the first key

array_push($popularContentArray, $most_frequent);
array_push($popularContentArray, $most_frequent2);
array_push($popularContentArray, $most_frequent3);

function generateVideoCard($item, $client){
    echo '<div class="videoCard"><a href="./watch.php?v=' . $item['videoID']['S'] . '">';

    $cmd = $client->getCommand('GetObject', [
        'Bucket' => Constants::$bucketName,
        'Key' => $item['videoID']['S'] . '/' . $item['videoID']['S'] . '-thumbnail.jpg'
        ]
    );

    $request = $client->createPresignedRequest($cmd, '+20 minutes');

    // Get the actual presigned-url
    $presignedUrl = (string)$request->getUri();

    echo '<img class="videoCard__thumbnail" src="' . $presignedUrl . '">';

    echo '</a><a class="videoCard__title" href="./watch.php?v=' . $item['videoID']['S'] . '">' . $item['title']['S'] . '</a><span class="videoCard__tags">';

    if(array_key_exists("tags", $item)){
        $tags = implode(', ', $item['tags']['SS']);
        echo $tags;
    }

    echo "<span style='display:block'>" . Functions::timestampToString($item['timestamp']['S']) . "</span>";

    echo '</span></div>';
}
?>

<div class="container">
    <div class="search-buttons">
    <?php
    foreach($popularContentArray as $item){
        echo "<a class='btn' href='./results.php?search_query=" . $item . "'>" . $item . "</a>";
    }
    ?>
    </div>
    <h1 class="page-title">Recent Videos</h1>
    <div class="recentVideos">
<?php
foreach ($items as $item) {
    generateVideoCard($item, $s3client);
    }
?>


</div>
<?php

$numVideos = count($items);

if($numVideos >= $videosPerPage){
?>
<a href="./videos.php">See all Videos</a>
<?php
}
?>
</div>
</div>
