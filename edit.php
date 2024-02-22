<?php
require_once 'inc/classes/Constants.php';
require 'inc/classes/DynamoDBWrapper.php';
require 'inc/classes/S3Wrapper.php';

// If video query string is blank, redirect to index page
if($_GET['v'] == ''){
    header('Location: '. '/');
}

echo __DIR__;

$dynamoDB = new DynamoDBWrapper(Constants::$region, Constants::$profile);
$dynamoClient = $dynamoDB->getDynamoClient();

$s3 = new S3Wrapper(Constants::$region);
$s3Client = $s3->getS3Client();

// Get video ID from query string
$videoID = $_GET['v'];

edit_submit();

$result = $dynamoClient->getItem(array(
    'ConsistentRead' => true,
    'TableName' => 'councilVideos',
    'Key'       => array(
        'videoID'   => array('S' => $videoID),
    )
));

// Video vars
$videoTitle = $result['Item']['title']['S'];
$videoDescription = $result['Item']['description']['S'];
$videoTags = $result['Item']['tags']['SS'];



$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => Constants::$bucketName,
    'Key' => $videoID . '/' . $videoID . '-thumbnail.jpg'
    ]
);

$thumbnailURL = $s3Client->createPresignedRequest($cmd, '+10 minutes');

// Get the actual presigned-url
$thumbnailURL = (string)$thumbnailURL->getUri();

$title = 'Edit ' . $videoTitle . ' - American Immigration Council';

function edit_submit(){
if(isset($_POST['submit'])){
    $dynamoDB = new DynamoDBWrapper(Constants::$region, Constants::$profile);
$dynamoClient = $dynamoDB->getDynamoClient();

    $newVideoTitle = $_POST['title'];
    $newVideoDescription = $_POST['description'];
    $newVideoTags = array_column(json_decode($_POST['tags']), 'value');

    $videoID = $_GET['v'];

    $response = $dynamoClient->updateItem(array(
        'TableName' => 'councilVideos',
        'Key' => [
            'videoID' => ['S' => $videoID]
        ],
        'AttributeUpdates' => array(
            'title' => [
                'Value' => array(
                    'S' => $newVideoTitle
                ),
                'Action' => 'PUT'
            ],
            'titleLowercase' => [
                'Value' => array(
                'S' => strtolower($newVideoTitle),
            ),
                'Action' => 'PUT'
            ],
            'description' => [
                'Value' => array(
                'S' => $newVideoDescription,
            ),
                'Action' => 'PUT'
            ],
            'descriptionLowercase' => [
                'Value' => array(
                'S' => strtolower($newVideoDescription),
            ),
                'Action' => 'PUT'
        ],
            'tags' => [
                'Value' => array(
                'SS' => $newVideoTags,
            ),
                'Action' => 'PUT'
        ],
            'tagsLowercase' => [
                'Value' => array(
                'SS' => array_map('strtolower', $newVideoTags)
            ),
                'Action' => 'PUT'
            ]
        )
    ));
}
}

include('header.php');
?>

<div class="container">

   <div class="videoEdit__wrapper">
   <h1>Edit Video</h1>
   <img class="videoEdit__thumbnail" src="<?php echo $thumbnailURL; ?>">
       <form action="./edit.php?v=<?php echo $videoID; ?>" method="POST" enctype="multipart/form-data" id="videoEdit" name="videoEdit">
       <div class="input-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="<?php echo $videoTitle; ?>">
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" value="<?php echo $videoDescription; ?>">
                </div>

                <div class="input-group">
                    <label for="tags">Tags</label>
                    <input type="text" name="tags" id="tags">
                </div>


                <a href="#" onclick="myFunction()" class="delete-text">
<svg style="position:relative; top:10px;" fill="#FF2600" width="32px" height="32px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
 <path d="m77.828 32h-12.828l-1.9297-6.75c-1.2266-4.2852-5.1406-7.2422-9.6016-7.25h-6.9375c-4.4688-0.003906-8.3945 2.957-9.6211 7.25l-1.9102 6.75h-12.828c-2.1094-0.042969-3.9102 1.5156-4.1719 3.6094-0.10938 1.1289 0.26172 2.2461 1.0234 3.0859 0.76172 0.83594 1.8438 1.3086 2.9766 1.3047h3c0 0.3125 0.015625 0.62109 0.050781 0.92969l3.7891 32.23c0.58984 5.043 4.8633 8.8438 9.9414 8.8398h22.438c5.0703 0 9.3398-3.7969 9.9297-8.8281l3.7891-32.23h0.003906c0.035156-0.3125 0.058594-0.62891 0.058594-0.94141h3c1.1328 0.003906 2.2148-0.46875 2.9766-1.3047 0.76172-0.83984 1.1328-1.957 1.0234-3.0859-0.26172-2.0938-2.0625-3.6523-4.1719-3.6094zm-33.227-4.5508c0.24609-0.85938 1.0312-1.4531 1.9297-1.4492h6.9492c0.89062 0 1.6758 0.59375 1.918 1.4492l1.3008 4.5508h-13.398zm20.867 25.551-0.94141 8-1.3164 11.23c-0.11719 1.0117-0.98047 1.7773-2 1.7695h-22.43c-1.0234 0.007812-1.8828-0.75781-2-1.7695l-1.3125-11.23-0.94141-8-1.5273-13h34z"/>
</svg>
 Delete Video</a>

                <div class="aic-button__container" style="margin-top:2rem">
                   <input type="submit" value="Submit" name="submit" class="aic-button aic-button--primary aic-button--large">
                </div>
       </form>
   </div>
</div>

<p class="message"></div>

<footer></footer>

<script>
    var tagsfromDB = <?php echo json_encode($videoTags); ?>;
    var tagsEl = document.querySelector("input[name='tags']");
    var tagify = new Tagify(tagsEl);
    tagify.addTags(tagsfromDB);

    function myFunction() {
  confirm("Are you sure you want to delete this video?");
}

function myFunction() {
  confirm("Are you sure you want to delete this video?");
}
</script>
</body>
</html>

