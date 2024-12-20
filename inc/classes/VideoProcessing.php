<?php
use Aws\S3\S3Client;

require_once('inc/config.php'); // Include Constants
require_once('inc/classes/Constants.php'); // Include Constants

class VideoProcessing
{
    static public function createVideoThumbnail($videoFile, $outputID)
    {

        $thumbnailSize = "450x253";
        $pathToThumbnail = "uploads/videos/thumbnails/" . $outputID . "-thumbnail.jpg";

        $baseDomain = $_SERVER['SERVER_NAME'];

        if (str_contains($baseDomain, "localhost")) {
            $ffmpegPath = "ffmpeg/mac/regular-xampp/ffmpeg";
        } else {
            $ffmpegPath = "ffmpeg";
        }

        $cmd = "$ffmpegPath -i $videoFile -ss 10 -s $thumbnailSize -vframes 1 $pathToThumbnail 2>&1";

        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);

        if ($returnCode != 0) {
            echo $returnCode;
            //Command failed
            foreach ($outputLog as $line) {
                echo "Error: " . $line . "<br>";
            }
            return false;
        }
        // $s3client = new Aws\S3\S3Client(['region' => Constants::$region, 'version' => Constants::$version]);
    }

    static public function uploadThumbnailToS3($videoID, $file)
    {
        $s3client = new Aws\S3\S3Client([
            'region' => Constants::$region,
            'version' => Constants::$version,
            'credentials' => [
                'key' => Constants::$accessKey,
                'secret' => Constants::$secretKey,
            ],
        ]);
        try {
            $s3client->putObject([
                'Bucket' => Constants::$bucketName,
                'Key' => $videoID . '/' . $videoID . '-thumbnail.jpg',
                'SourceFile' => $file
            ]);
        } catch (Exception $exception) {
            echo "Failed to upload  with error: " . $exception->getMessage();
            exit("Please fix error with file upload before continuing.");
        }
    }
}
?>