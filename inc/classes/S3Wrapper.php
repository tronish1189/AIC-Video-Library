<?php
use Aws\Exception\AwsException;
use Aws\S3\S3Client;

Class S3Wrapper{
    private $client;

    public function __construct($region)
    {
        $this->client = new Aws\S3\S3Client([
            'region' => $region,
            'version' => 'latest'
        ]);
    }

    public function getS3Client()
    {
        return $this->client;
    }
}

?>