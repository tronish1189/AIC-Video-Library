<?php

include 'inc/classes/VideoActions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arg1 = $_POST['videoID'];

    $myClass = new VideoActions();
    $result = $myClass->deleteVideo($arg1);
}