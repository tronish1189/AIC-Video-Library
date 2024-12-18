<?php
use Aws\DynamoDb\DynamoDbClient;

class Account
{
    private $errorArray = array();

    public function login($em, $pw)
    {
        $pw = hash("sha512", $pw);

        $dynamoClient = new DynamoDbClient([
            // 'profile' => Constants::$profile,
            'region' => Constants::$region,
            'version' => Constants::$version,
            'credentials' => [
                'key' => Constants::$accessKey,
                'secret' => Constants::$secretKey,
            ],
        ]);

        $dynamoSearchQuery = $dynamoClient->scan(array(
            'TableName' => 'videoLibraryUsers',
            'FilterExpression' => 'emailID = :emailID AND password = :password',
            'ExpressionAttributeValues' => array(
                ":emailID" => array('S' => $em),
                ":password" => array('S' => $pw),
            )
        ));

        if (count($dynamoSearchQuery['Items']) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function register($fn, $ln, $em, $em2, $pw, $pw2)
    {
        $this->validateFirstName($fn);
        $this->validateLastName($ln);
        $this->validateEmails($em, $em2);
        $this->validatePasswords($pw, $pw2);

        if (empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $ln, $em, $pw);
        } else {
            return false;
        }
    }

    public function insertUserDetails($fn, $ln, $em, $pw)
    {
        $pw = hash("sha512", $pw);

        $dynamoClient = new DynamoDbClient([
            // 'profile' => Constants::$profile,
            'region' => Constants::$region,
            'version' => Constants::$version,
            'credentials' => [
                'key' => Constants::$accessKey,
                'secret' => Constants::$secretKey,
            ],
        ]);

        $response = $dynamoClient->putItem(array(
            'TableName' => 'videoLibraryUsers',
            'Item' => array(
                'emailID' => array('S' => $em),
                'first_name' => [
                    'S' => $fn,
                ],
                'last_name' => [
                    'S' => $ln,
                ],
                'password' => [
                    'S' => $pw,
                ],
            )
        ));

        return $response;
    }

    private function validateFirstName($fn)
    {
        if (strlen($fn) > 25 || strlen($fn) < 2) {
            array_push($this->errorArray, Constants::$firstNameCharacters);
        }
    }

    private function validateLastName($ln)
    {
        if (strlen($ln) > 25 || strlen($ln) < 2) {
            array_push($this->errorArray, Constants::$lastNameCharacters);
        }
    }

    private function validateUsername($un)
    {
        if (strlen($un) > 25 || strlen($un) < 5) {
            array_push($this->errorArray, Constants::$usernameCharacters);
            return;
        }

        $query = $this->con->prepare("SELECT username FROM users WHERE username=:un");
        $query->bindParam(":un", $un);
        $query->execute();

        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$usernameTaken);
        }
    }

    private function validateEmails($em, $em2)
    {
        if ($em != $em2) {
            array_push($this->errorArray, Constants::$emailsDoNotMatch);
            return;
        }

        if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
    }

    private function ValidatePasswords($pw, $pw2)
    {
        if ($pw != $pw2) {
            array_push($this->errorArray, Constants::$passwordsDoNotMatch);
            return;
        }

        if (preg_match("/[^A-Za-z0-9]/", $pw)) {
            array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
            return;
        }

        if (strlen($pw) > 30 || strlen($pw) < 5) {
            array_push($this->errorArray, Constants::$passwordLength);
        }
    }

    public function getError($error)
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }
}
