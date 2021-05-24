<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


$BUCKET_NAME = '';
$IAM_KEY = '';
$IAM_SECRET = '';


require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucket = '';

// Instantiate the client.
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'ap-south-1',
    'credentials' => array(
        'key' => $IAM_KEY,
        'secret'  => $IAM_SECRET,
    )
]);

// Use the high-level iterators (returns ALL of your objects).
try {
    $results = $s3->getPaginator('ListObjects', [
        'Bucket' => $bucket
    ]);

    echo "<h2> List of files </h2><br><br>";
    $objects = $s3->getIterator('ListObjects', array(
        "Bucket" => $bucket,
        "Prefix" => 'test_example/' //must have the trailing forward slash "/"
    ));
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of files</title>
</head>

<body>
    <table>
        <tr>
            <th>File</th>
            <th>Download link</th>
        </tr>
        <?php foreach ($objects as $object) {
            if ($object["Size"] > 0) {
        ?>
                <tr>
                    <td><?php echo $object["Key"]; ?></td>
                    <td><a href="<?php echo $s3->getObjectUrl($bucket, $object['Key']) ?>"> Download </a></td>
                </tr>
        <?php }
        } ?>
    </table>
</body>

</html>