<?php
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

include 'vendor/autoload.php';

Class DynamoDBWrapper{
    private $client;

    public function __construct($region, $profile)
    {
        $this->client = new DynamoDbClient([
            'region' => $region,
            // 'profile' => $profile,
            'version' => 'latest'
        ]);
    }

    public function getDynamoClient()
    {
        return $this->client;
    }

    public function getItem($client, $tableName, $attributeName, $value)
    {
        return $client->getItem(array(
            'ConsistentRead' => true,
            'TableName' => $tableName,
            'Key'       => array(
                $attributeName   => array('S' => $value),
            )
        ));
    }
}

?>