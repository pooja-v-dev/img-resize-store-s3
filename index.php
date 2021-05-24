<!DOCTYPE html>
<html>

<body>

    <!-- <form action="upload.php" method="post" enctype="multipart/form-data">
        Select image to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload File" name="submit">
    </form> -->
    <form action="upload_img.php" method="post" enctype="multipart/form-data">
        <h2>Choose file:</h2>
        <div class="img_upld">
            <input type="file" name="uploads" accept=".jpeg" name="fileToUpload" id="fileToUpload" />
            <input type="submit" value="Upload" name="submit" />
        </div>
    </form>

</body>

</html>