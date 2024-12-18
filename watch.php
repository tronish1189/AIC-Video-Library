<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Guzzle\Http\Client;
use Aws\DynamoDb\DynamoDbClient;

require 'vendor/autoload.php'; // Include the AWS SDK for PHP
require_once 'inc/classes/Constants.php';

if ($_GET['v'] == '') {
    header('Location: ' . '/');
}

$s3client = new Aws\S3\S3Client([
    'region' => Constants::$region,
    'version' => Constants::$version,
    'credentials' => [
        'key' => Constants::$accessKey,
        'secret' => Constants::$secretKey,
    ],
]);

$dynamoClient = new DynamoDbClient([
    // 'profile' => Constants::$profile,
    'region' => Constants::$region,
    'version' => Constants::$version,
    'credentials' => [
        'key' => Constants::$accessKey,
        'secret' => Constants::$secretKey,
    ],
]);

$videoID = $_GET['v'];

$result = $dynamoClient->getItem(array(
    'ConsistentRead' => true,
    'TableName' => 'councilVideos',
    'Key' => array(
        'videoID' => array('S' => $videoID),
    )
));

$videoTitle = $result['Item']['title']['S'];
$videoDescription = $result['Item']['description']['S'];

$videoDate = $result['Item']['timestamp']['S'];
$videoDate = date('F d, Y', strtotime($videoDate));

if (isset($result['Item']['uploaded_by'])) {
    $uploadedByName = $result['Item']['uploaded_by']['S'];
}


$cmd = $s3client->getCommand('GetObject', [
    'Bucket' => Constants::$bucketName,
    'Key' => $videoID . '/' . $videoID . '.mp4'
]);

$request = $s3client->createPresignedRequest($cmd, '+20 minutes');

// Get the actual presigned-url
$presignedUrl = (string) $request->getUri();

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

$title = $videoTitle . ' - American Immigration Council';
-
    include('header.php');
?>




<div class="video-details-page">
    <div class="container">
        <div class="video__wrapper">
            <video id="my-video" class="video-js vjs-default-skin" controls preload="auto" width="860" height="480"
                poster="" data-setup="{}">
                <source src="<?php echo $presignedUrl ?>" type="video/mp4" />
            </video>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <h1 class="video__title"><?php echo $videoTitle; ?></h1>
                    <span class="video__uploadedBy">Uploaded on <?php echo $videoDate; ?>
                        <?php if ($uploadedByName):
                            echo "&#183; Uploaded By " . $uploadedByName;
                        endif; ?></span>

                </div>
                <div class="video__actions">
                    <a href="edit.php?v=<?php echo $videoID; ?>">
                        <svg width="30px" height="30px" version="1.1" viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path d="m91.668 24.594-16.262-16.262-11.266 11.27 16.258 16.258z" />
                                <path d="m25 75 17.887-1.625 30.777-30.781-16.258-16.258-30.781 30.777z" />
                                <path d="m62.5 91.668h-54.168v-8.3359h54.168z" fill-rule="evenodd" />
                            </g>
                        </svg>
                    </a>
                    <a download href="<?php echo $presignedUrl ?>">
                        <svg width="32px" height="32px" version="1.1" viewBox="0 0 100 100"
                            xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path
                                    d="m72.082 36.668-17.914 17.914v-42.082h-8.3359v42.082l-17.914-17.914-5.8359 5.832 27.918 27.918 27.918-27.918z" />
                                <path d="m27.082 79.168h45.832v8.332h-45.832z" />
                            </g>
                        </svg>
                    </a>
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