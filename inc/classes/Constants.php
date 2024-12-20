<?php

use Dotenv\Dotenv;
include 'vendor/autoload.php';

class Constants
{
    public static $region = 'region';
    public static $version = 'version';
    public static $accessKey = 'accessKey';
    public static $secretKey = 'secretKey';
    public static $bucketName = 'video-file-storage';
    public static $firstNameCharacters = "Your first name must be between 2 and 25 characters.";
    public static $lastNameCharacters = "Your last name must be between 2 and 25 characters.";
    public static $usernameCharacters = "Your username must be between 5 and 25 characters.";
    public static $usernameTaken = "This username already exists.";
    public static $emailsDoNotMatch = "Your emails do not match.";
    public static $emailInvalid = "Please enter a valid email address.";
    public static $emailTaken = "This email is already in use.";
    public static $passwordsDoNotMatch = "Your passwords do not match.";
    public static $passwordNotAlphanumeric = "Your password can only contain letters and numbers.";
    public static $passwordLength = "Your password must be between 5 and 30 characters.";
    public static $loginFailed = "Your username or password isn't correct.";

    public static function initialize()
    {
        // Load .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../"); // Adjust path to your .env location
        $dotenv->load();

        self::$region = $_ENV['AWS_REGION'];
        self::$version = $_ENV['AWS_VERSION'];
        self::$accessKey = $_ENV['AWS_ACCESS_KEY'];
        self::$secretKey = $_ENV['AWS_SECRET_KEY'];
    }
}

Constants::initialize();