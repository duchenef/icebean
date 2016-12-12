<?php

// verification de la presence d'une valeur de hauteur d'image en parametre;
// verification que la valeur en parametre est bien numerique;
// si ok variable nh prend la valeur du parametre h;
// si une des deux conditions n'est pas remplie valeur par defaut de nh est 120 pixels;

if (!isset($_GET["h"]) or !ctype_digit($_GET["h"]))
{
$nh = 120;
}   
else
{
$nh = urlencode($_GET["h"]);
}

// verification de la presence d'une valeur d'url;

if (!isset($_GET["url"]))
{
die("INVALID URL");
}   
else
{
$imagepath = urldecode($_GET["url"]);
   $size = getimagesize($imagepath);
   if ($size[0] < 2) {
   $imagepath = "no_image.jpg";}
}

// calcul des nouvelles dimensions;
list($width, $height) = getimagesize($imagepath); // recupere les dimensions de l'image;
$nh = $nh; // nouvelle hauteur prend la valeur de nouvelle hauteur A l'avenir prendra la valeur d'un input;
$r = $height / $nh;
$nw = $width / $r;

// resample de l'image;

$image_p = imagecreatetruecolor($nw, $nh);
$image=imagecreatefromjpeg($imagepath);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $nw, $nh, $width, $height);


//affichage de l'image;
header('Content-Type: image/jpeg');
imagejpeg($image_p, null, 100);
//echo $imagepath;
//echo "<BR>";
//echo var_dump($size);
//echo "<BR>";
//echo $size[0];

// sauvegarde de l'image;

if (isset($_GET["fn"]))
{
$filename = $_GET["fn"];
imagejpeg($image_p, '../images/'.$filename, 100);
}   


?>