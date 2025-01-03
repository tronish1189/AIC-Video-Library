<?php
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

class S3Wrapper
{
    private $client;

    public function __construct($region)
    {
        $this->client = new Aws\S3\S3Client([
            'region' => $region,
            'version' => 'latest',
            'credentials' => [
                'key' => Constants::$accessKey,
                'secret' => Constants::$secretKey,
            ],
        ]);
    }

    public function getS3Client()
    {
        return $this->client;
    }
}

?>