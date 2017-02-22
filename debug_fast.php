<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
    <title>Fast2Mdr</title>   
    <link rel="stylesheet" type="text/css" href="css/icebean.css">
</head>
<?php
// capture de l'isbn dans l'url
if (!isset($_GET["isbn"]))
{
die("INVALID URL");
}
else
{
$isbn = $_GET["isbn"];
}
// appel fonction fast
$marcArray = [];
$readArray = [];

include 'resources/function_fast.php';
$fastresults = fast2mdr($isbn);
$marcArray = $fastresults[0];
$readArray = $fastresults[1];

// affichage des resultats en html et caches pour javascript
$j = 0;
echo '<div id="fastwrapper">';
foreach ($marcArray as $value) {
  echo '<div class ="fast" id="fastdisplay'.$j.'">'.$readArray[$j].'</div>';
  echo '<button class="buttons" id="copy-button'.$j.'" data-clipboard-target="#fast'.$j.'">Copy</button>';
  echo '<div class ="hidden" id="fast'.$j.'" style="display: none;">'.$value.'</div>'; 
  $j++;
}

//var_dump($marcArray) ;
?>

<script src="js/clipboard.min.js"></script>
<script type="text/javascript">
new Clipboard('.buttons', {
            text: function(trigger) {
                console.log(JSON.parse(trigger.nextElementSibling.textContent));
                return JSON.parse(trigger.nextElementSibling.textContent);
            }
        });
</script>
