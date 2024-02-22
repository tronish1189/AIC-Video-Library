<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Guzzle\Http\Client;
use Aws\DynamoDb\DynamoDbClient;

require 'vendor/autoload.php'; // Include the AWS SDK for PHP
require_once 'inc/classes/Constants.php';

if($_GET['v'] == ''){
    header('Location: '. '/');
}

$s3client = new Aws\S3\S3Client(['region' => Constants::$region, 'version' => Constants::$version]);

$dynamoClient = new DynamoDbClient([
    'profile' => Constants::$profile,
    'region'  => Constants::$region,
    'version' => Constants::$version
]);

$videoID = $_GET['v'];

$result = $dynamoClient->getItem(array(
    'ConsistentRead' => true,
    'TableName' => 'councilVideos',
    'Key'       => array(
        'videoID'   => array('S' => $videoID),
    )
));

$videoTitle = $result['Item']['title']['S'];
$videoDescription = $result['Item']['description']['S'];

$videoDate = $result['Item']['timestamp']['S'];
$videoDate = date('F d, Y', strtotime($videoDate));

if(isset($result['Item']['uploaded_by'])){
$uploadedByName = $result['Item']['uploaded_by']['S'];
}


$cmd = $s3client->getCommand('GetObject', [
    'Bucket' => Constants::$bucketName,
    'Key' => $videoID . '/' . $videoID . '.mp4'
]);

$request = $s3client->createPresignedRequest($cmd, '+20 minutes');

// Get the actual presigned-url
$presignedUrl = (string)$request->getUri();

// try {
//     $file = $s3client->getObject([
//         'Bucket' => 'video-file-storage',
//         'Key' => 'MichaelLopetrone_CDPHP.mp4',
//     ]);
//     // $body = $file->get('Body');
//     // echo $body;
//     // $body->rewind();
//     // echo "Downloaded the file and it begins with: {$body->read(26)}.\n";
// } catch (Exception $exception) {
//     echo "Failed to download with error: " . $exception->getMessage();
//     exit("Please fix error with file downloading before continuing.");
// }

$title = $videoTitle . ' - American Immigration Council';-
include('header.php');
?>




<div class="video-details-page">
    <div class="container">
    <div class="video__wrapper">
        <video id="my-video"
            class="video-js vjs-default-skin"
            controls
            preload="auto"
            width="860"
            height="480"
            poster=""
            data-setup="{}"
>
        <source src="<?php echo $presignedUrl ?>" type="video/mp4" />
        </video>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
            <h1 class="video__title"><?php echo $videoTitle; ?></h1>
                <span class="video__uploadedBy">Upload on <?php echo $videoDate; ?></span>
                <?php if($uploadedByName): ?>
                <span class="video__uploadedBy"> | Upload By <?php echo $uploadedByName; ?></span>             <?php endif; ?></div>
            <div class="video__actions">
                <a href="edit.php?v=<?php echo $videoID; ?>">
                    <svg width="30px" height="30px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="m91.668 24.594-16.262-16.262-11.266 11.27 16.258 16.258z"/>
                            <path d="m25 75 17.887-1.625 30.777-30.781-16.258-16.258-30.781 30.777z"/>
                            <path d="m62.5 91.668h-54.168v-8.3359h54.168z" fill-rule="evenodd"/>
                        </g>
                    </svg>
                </a>
                <a download href="<?php echo $presignedUrl ?>">
                    <svg width="32px" height="32px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="m72.082 36.668-17.914 17.914v-42.082h-8.3359v42.082l-17.914-17.914-5.8359 5.832 27.918 27.918 27.918-27.918z"/>
                            <path d="m27.082 79.168h45.832v8.332h-45.832z"/>
                        </g>
                    </svg>
                </a>
                <button class="copy-code">
                    <svg xmlns="http://www.w3.org/2000/svg" width="55.878" height="31.364" viewBox="0 0 55.878 31.364" fill="black">
                      <path id="np_code_445309_000000" d="M38.785,25.937a1.948,1.948,0,0,0-1.807,1.442l-7.8,27.294a1.95,1.95,0,1,0,3.737,1.076l7.8-27.294a1.951,1.951,0,0,0-1.929-2.518ZM24.427,30.507a1.966,1.966,0,0,0-.853.284l-15.6,9.1a1.95,1.95,0,0,0,0,3.371l15.6,9.1a1.95,1.95,0,1,0,1.95-3.371L12.811,41.575l12.713-7.412a1.95,1.95,0,0,0-1.1-3.655Zm20.775,0a1.95,1.95,0,0,0-.833,3.655l12.713,7.412L44.37,48.987a1.95,1.95,0,1,0,1.95,3.371l15.6-9.1a1.95,1.95,0,0,0,0-3.371l-15.6-9.1a1.941,1.941,0,0,0-1.117-.284Z" transform="translate(-7.007 -25.936)"/>
                    </svg>
                </button>
            </div>
        </div>
        <p class="video__description"><?php echo $videoDescription; ?></p>
    </div>
    </div>
</div>

<footer></footer>

<script src="node_modules/video.js/dist/video.min.js"></script>
</body>
</html>

