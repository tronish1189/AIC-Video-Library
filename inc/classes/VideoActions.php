<?php
use Aws\DynamoDb\DynamoDbClient;

require_once('inc/config.php'); // Include Constants
require_once('inc/classes/Constants.php'); // Include Constants
require_once('inc/classes/DynamoDBWrapper.php');
require_once('inc/classes/S3Wrapper.php');

class VideoActions
{
    static public function deleteVideo($videoID)
    {

        $dynamoDB = new DynamoDBWrapper(Constants::$region, Constants::$version);
        $dynamoClient = $dynamoDB->getDynamoClient();

        $result = $dynamoClient->deleteItem(array(
            'ConsistentRead' => true,
            'TableName' => 'councilVideos',
            'Key' => array(
                'videoID' => array('S' => $videoID),
            )
        ));

        $s3client = new Aws\S3\S3Client([
            'region' => Constants::$region,
            'version' => Constants::$version,
            'credentials' => [
                'key' => Constants::$accessKey,
                'secret' => Constants::$secretKey,
            ],
        ]);

        $objects = $s3client->listObjectsV2([
            'Bucket' => Constants::$bucketName,
            'Prefix' => $videoID . '/',
        ]);

        if (isset($objects['Contents'])) {
            // Collect the keys of the objects to be deleted
            $keys = [];
            foreach ($objects['Contents'] as $object) {
                $keys[] = ['Key' => $object['Key']];
            }

            // Delete the objects
            if (!empty($keys)) {
                $result = $s3client->deleteObjects([
                    'Bucket' => Constants::$bucketName,
                    'Delete' => [
                        'Objects' => $keys,
                    ],
                ]);
            }
        }

        header("Location: index.php");
    }
}
?>