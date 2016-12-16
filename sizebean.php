<!DOCTYPE html>
<html>

<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
    <title>IceBean</title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/sizebean.css">
</head>

    
<body onload="document.forms.main_form.isbn.focus()">
  <version><verysmalli>sizebean v1.4 20161216fd</verysmalli></version>

<?php


// nettoyage du cache upload 
    //Repertoire images
    $imagesdir = "uploads/";
    // Pour chaque fichier du repertoire
    foreach(glob($imagesdir.'*.*') as $file){
        unlink($file);
    }

$filenamelt="cover.jpg";

// recuperation de l'isbn dans le formulaire
  // pre-traitement
  $url = urlencode($_POST["url"]);
  $url_status = "url value found";

if (isset($url))

{
  $isbn = urlencode($_POST["url"]);
    $url_status = "url value found";

}   

else
{
$url_status = "no url value found, search couldn't be performed";
}

// recuperer la valeur du champ height et verifier qu'il existe et qu'il s'agisse bien d'un nombre;
// agir en fonction;

if (isset($_POST["nh"]) and ctype_digit($_POST["nh"]))
{
$nh = $_POST["nh"];
$nh_status = "height value used for resizing: ";
} 
  
else
{
$nh_status = "no height provided, default value : ";
$nh = "120";
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadname = basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $upload_status = "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $upload_status = "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    $upload_status = "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    $upload_status = "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $upload_status = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $upload_status = "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $upload_status = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $actual_link = substr($actual_link, 0, (strlen($actual_link)-12));
        $url = $actual_link."uploads/$uploadname";
        $upload_status= $url;
    } else {
        $upload_status= "Sorry, there was an error uploading your file.";
    }
}


?>

<table width = '1280'>
  <TR height='38px'>
        <TH align='left' colspan='4'><img  align='middle' src='resources/icebean2.png'></TH>
  </TR>
  <TR>
    <TD colspan='1'><small>Image url</small></TD>
    <TD colspan='2'>
      <FORM NAME="main_form" ACTION="sizebean.php" METHOD="POST">
      <INPUT TYPE="text" NAME="url" VALUE="<?PHP echo $url; ?>" MAXLENGTH="255"<BR>
    </TD>
    <TD colspan='1'>  
      <INPUT TYPE="submit" NAME="SEND">
      </FORM>
    </TD>
  </TR>
  <TR>
    <TD colspan='1'><small>Image upload</small></TD>
    <TD colspan='2'>
      <form enctype="multipart/form-data" method="post" action="sizebean.php"> 
      <input type="file" name="fileToUpload" id="fileToUpload" />
    </TD>
    <TD colspan='1'>
      <input type="submit" value="Upload" />
      </form>
    </TD>
  </TR>

  <TR>
    <TH align='left' colspan='4'>Options</TH>
  </TR>
  <TR>
    <TD colspan='1'><small>Size (in pixels)</small></TD>
    <TD colspan='2'><INPUT TYPE="integer" NAME="nh" VALUE="<?PHP echo $nh; ?>" MAXLENGTH="3"</TD>
    <TD colspan='1'></TD>
  </TR>
</table>

</body>
</html>

<?php

// Affichage des resultats;
echo "<BR>";
echo "<table width='1280'>";

//Cover image

  echo "<tr>";
    echo "<th align='left' colspan='4'>Cover Image</th>";
  echo "</tr>";
  echo "<tr style='height:136px'>";
    echo "<td><center>";
    echo "<img align='middle' src='resources/resizer.php?url=".$url."&h=".$nh."&fn=".$filenamelt."'></center></td>";
  echo "</tr>";
echo "</table";

// Output log

echo "<table width = '1280'><tr><th align='left'>Output log </th></tr><td>";
echo "<small>";
echo $url_status.$url."<BR>";
echo $nh_status.$nh." pixels.<BR>";
echo "Default filename: ".$filenamelt."<BR>";
echo  "Server's actual path: ".$actual_link."<BR>";
echo  "Upload status: ".$upload_status."<BR>";
?>

<tr>
<td align='right'><FORM NAME="back" ACTION="documentation.htm" METHOD="POST">
      <input type='button' value='the Icebean' onClick=window.location.href='index.php'>
</td></tr></form>
</table>
</body>
</html>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-12520299-1', 'auto');
  ga('send', 'pageview');

</script>