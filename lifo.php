<?php

//phpinfo();
error_reporting(~0);
ini_set('display_errors', 1);

$json_link="https://api.instagram.com/v1/users/self/media/recent/?access_token=5372234506.1677ed0.063c4aa88499431a8fa679d4dd9085f0&count=13";
//$json_link="https://api.instagram.com/v1/users/self/media/recent/?access_token={$access_token}&count={$photo_count}";
//$json_link = "test.json";
$json = file_get_contents($json_link) || die("erreur get content");
$obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING); 

var_dump($obj);
