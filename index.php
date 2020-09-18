<?php
  if($_SERVER["REQUEST_METHOD"] === "POST"):
      if(isset($_FILES['userfile']) && isset($_POST['comment']) && isset($_POST['g-token'])):
        $comment = filter_var($_POST['comment'],FILTER_SANITIZE_STRING);
        $file = $_FILES['userfile'];
        $error = "";
        $success = "";
        //recaptcha Info
        $secretKey = "6LdibLYZAAAAAD6LjIQ6dHEUm_0ULGwM9ChKfD2n";
        $token = $_POST['g-token'];
        $userIp = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token&remoteip=$userIp";
        $request = file_get_contents($url);
        $apiResponse  = json_decode($request);
        //File Info
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];

        $allowedTypes = ['php','md','css','html','js'];
        $fileNameExploded = explode('.',$fileName);
        $fileType = array_pop($fileNameExploded);

        if($apiResponse->success){
          if(in_array(strtolower($fileType),$allowedTypes)){
            if($fileSize < 10000){
                if($fileError === 0){
                    if(move_uploaded_file($fileTmpName,"uploads/".uniqid(strtolower($fileType."."),true).".".$fileName)){
                        $success = "<div class='alert success'><p>File uploaded successfully</p></div>";
                    }else{
                      $error = "<div class='alert danger'><p>Possible File Upload <strong>Attack</strong>!</p></div>";
                    }
                  }else{
                      $error = "<div class='alert danger'><p>File Upload <strong>Failed</strong>!</p></div>";
                  }
              }else{
                  $error = "<div class='alert danger'><p>File Size Must Be Less Than 10000KB: <strong>$fileSize</strong></p></div>";
              }
          }else{
            $error = "<div class='alert danger'><p>This File Type is Not Allowed : .<strong>$fileType</strong></p></div>";
          }
        }else{
          $error = "Sorry ... Can Not Upload This File";
        }
      endif;

  endif;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>File Upload</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js?render=6LdibLYZAAAAAKGiZLe85Eh2VKQRO7krxRGFHlUv"></script>
    <script src="app.js" defer></script>
  </head>
  <body>

      <div class="container">
        <?php
          if(isset($success) && !empty($success)){
            echo $success;
          }else {
            if(isset($error)){
              echo $error;
            }
          }
        ?>
        <div class="form-container">
            <h2>Add Documents</h2>
            <form class="form-upload" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
                <input type="hidden" id="g-token" name="g-token">
                <div class="file-group">
                    <input type="file" name="userfile">
                    <span>Browse Files</span>
                </div>
                <textarea name="comment" placeholder="Add a comment"></textarea>
                <div class="msg">Accepted Files Types : php, md, js, html, css.</div>
                <input type="submit" value="Upload">
            </form>
        </div>
      </div>

      <script>
          grecaptcha.ready(function() {
            grecaptcha.execute('6LdibLYZAAAAAKGiZLe85Eh2VKQRO7krxRGFHlUv', {action: 'submit'}).then(function(token) {
              document.getElementById('g-token').setAttribute('value',token);
            });
          });
    </script>

  </body>
</html>
