<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Guzzle\Http\Client;
use Aws\DynamoDb\DynamoDbClient;

require 'vendor/autoload.php';
require_once 'inc/classes/Constants.php';

$s3client = new Aws\S3\S3Client(['region' => Constants::$region, 'version' => Constants::$version]);

$dynamoClient = new DynamoDbClient([
    // 'profile' => Constants::$profile,
    'region'  => Constants::$region,
    'version' => Constants::$version
]);

$searchQuery =  strtolower($_GET['search_query']);

$dynamoSearchQuery = $dynamoClient->scan(array(
    'TableName' => 'councilVideos',
    'FilterExpression' => 'contains(tagsLowercase, :searchQuery) OR contains(titleLowercase, :searchQuery) OR contains(descriptionLowercase, :searchQuery) OR contains(topicsLowercase, :searchQuery) OR contains(locationsLowercase, :searchQuery)',
    'ExpressionAttributeValues' => array(
        ":searchQuery" => array('S' => $searchQuery) ,
    )
));

$searchItems = $dynamoSearchQuery['Items'];

$title = "Search Results";
include('header.php');
?>


<div class="search-results-page">
    <div class="container">
<h1 style="margin-top:2rem">Search Results for "<?php echo $_GET['search_query']; ?>"</h1>
    <div class="resultsPageVideos">
<?php

foreach($searchItems as $item){
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

    echo '</a><a class="videoCard__title" href="./watch.php?v=' . $item['videoID']['S'] . '">' . $item['title']['S'];


    if(array_key_exists("tags", $item)){
        echo '</a><span class="videoCard__tags">';
        $tags = implode(', ', $item['tags']['SS']);
        echo $tags;
        echo '</span>';
    }

    echo "</div>";
}

?>
    </div>
</div>
</div>

<footer></footer>

</body>
</html>