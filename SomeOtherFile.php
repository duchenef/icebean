<?php
session_start();
$shirtColor = $_POST['shirt_color'];

$shirtColor2 = $_SESSION["shirtColor2"];

echo $shirtColor . "<hr>";
echo $shirtColor2;

echo "<pre><p>SESSION</p>";
print_r($_SESSION);
echo "</pre><hr>";

echo "<pre><p>POST</p>";
print_r($_POST);
echo "</pre>";

session_destroy();

?>