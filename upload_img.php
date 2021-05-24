<style>
    a{
        color: #000;
    }
    button{
        float: right;
        padding: 10px 15px;
        margin-right: 30%;
    }
</style>
<button><a href="index.php">Back</a></button>

<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucketName = '';
$IAM_KEY = '';
$IAM_SECRET = '';

$name = $_FILES["uploads"]["name"];
$tmpName = $_FILES["uploads"]["tmp_name"];
$type = $_FILES["uploads"]["type"];
$size = $_FILES["uploads"]["size"];
$errorMsg = $_FILES["uploads"]["error"];
$explode = explode(".",$name);
$extension = end($explode);

if(!$tmpName)
{
    echo "ERROR: Please choose file";
    exit();
}
else if($size > 5242880)
{
    echo "ERROR: Please choose less than 5MB file for uploading";
    unlink($tmpName);
    exit();
}
else if(!preg_match("/\.(jpg|png|jpeg)$/i",$name)) 
{
    echo "ERROR: Please choose the file only with the JPEG file format";
    unlink($tmpName);
    exit();
}
else if($errorMsg == 1)
{
    echo "ERROR: An unexpected error occured while processing the file. Please try again.";
    exit();
}

$uploaddir = __DIR__.'/uploads/';
$uploadfile = $uploaddir . basename($_FILES['uploads']['name']);

if (!file_exists($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}

$moveFile = move_uploaded_file($tmpName,$uploadfile);

if($moveFile != true)
{
    echo "ERROR: File not uploaded. Please try again";
    unlink($tmpName);
    exit();
}


// include_once("upld_fn.php");
$target = "uploads/$name";
$resize = "uploads/resized_$name";

$max_height = 360; 
$max_width  = 640;

upld_fn($target, $resize, $max_width, $max_height, $extension);

echo "<h2>Original image:-</h2> ";
echo "<img src='uploads/$name' /> <br/>";
echo "<h2>Resized image:-</h2> ";
$img_path = "uploads/resized_$name";
echo "<img src='$img_path' />";



function upld_fn($targett, $newcpy, $w, $h, $extn)
{

    list($origWidth, $origHeight) = getimagesize($targett);

    $ratio = $origWidth / $origHeight;

    if (($w / $h) > $ratio) {
        $w = $h * $ratio;
    }

    $img = "";
    $extn = strtolower($extn);

    if ($extn == "jpeg") {
       
        $img = imagecreatefromjpeg($targett);
    }
    $a = imagecreatetruecolor($w, $h);

    imagecopyresampled($a, $img, 0, 0, 0, 0, $w, $h, $origWidth, $origHeight);
    imagejpeg($a, $newcpy, 80);
}

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


$keyName = 'test_example/' . basename($_FILES["uploads"]['name']);
$pathInS3 = 'https://s3.ap-south-1.amazonaws.com/' . $bucketName . '/' . $keyName;

try {

    $file = $_FILES["uploads"]['tmp_name'];

    $s3->putObject(
        array(
            'Bucket' => $bucketName,
            'Key' =>  $keyName,
            'SourceFile' => $img_path,
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
