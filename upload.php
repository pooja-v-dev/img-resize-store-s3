<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucketName = '';
$IAM_KEY = '';
$IAM_SECRET = '';

try {

    $s3 = S3Client::factory(
        array(
            'credentials' => array(
                'key' => $IAM_KEY,
                'secret' => $IAM_SECRET
            ),
            'version' => 'latest',
            'region'  => 'ap-south-1'
        )
    );
} catch (Exception $e) {

    die("Error: " . $e->getMessage());
}


$keyName = 'test_example/' . basename($_FILES["fileToUpload"]['name']);
$pathInS3 = 'https://s3.ap-south-1.amazonaws.com/' . $bucketName . '/' . $keyName;

try {

    $file = $_FILES["fileToUpload"]['tmp_name'];

    $s3->putObject(
        array(
            'Bucket' => $bucketName,
            'Key' =>  $keyName,
            'SourceFile' => $file,
            'StorageClass' => 'REDUCED_REDUNDANCY',
            'ACL' => 'public-read'
        )
    );
} catch (S3Exception $e) {
    die('Error:' . $e->getMessage());
} catch (Exception $e) {
    die('Error:' . $e->getMessage());
}

echo '<h4>File uploaded successfully</h4>' . '<br>';
echo '<button><a href="get.php">View Listing</a></button>';
