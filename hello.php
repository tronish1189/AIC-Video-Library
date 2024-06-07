<?php

echo uniqid();

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\DynamoDb\DynamoDbClient;

/**
 * List your Amazon S3 buckets.
 *
 * This code expects that you have AWS credentials set up per:
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
 */


$client = new DynamoDbClient([
    // 'profile' => 'default',
    'region'  => 'us-east-2',
    'version' => 'latest'
]);

$result = $client->describeTable(array(
    'TableName' => 'councilVideos'
));

echo $result . '<br><br>';

$response = $client->putItem(array(
    'TableName' => 'councilVideos',
    'Item' => array(
        'videoID'   => array('S' => 'Turd'),
        'title' => [
            'S' => 'Turd Video',
        ],
    )
));

//Echoing the response is only good to check if it was successful. Status: 200 = Success
echo $response;


//Create a S3Client
$s3Client = new S3Client([
    // 'profile' => 'default',
    'region' => 'us-east-1',
    'version' => '2006-03-01'
]);

//Listing all S3 Bucket
$buckets = $s3Client->listBuckets();
foreach ($buckets['Buckets'] as $bucket) {
    echo $bucket['Name'] . "\n";
}
