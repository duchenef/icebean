<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

function google($isbn) {

$googleapikey = "AIzaSyC37NxL87kBJuKHS216Aihd5shu27Ch5Ko";
$urlr = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key='.$googleapikey;

$string = file_get_contents($urlr);

    $result = json_decode($string, true);
       $items = $result["totalItems"];
       $title = $result["items"] [0] ["volumeInfo"] ["title"];
       $author = $result["items"] [0] ["volumeInfo"] ["authors"] [0];
       $descr = $result["items"] [0] ["volumeInfo"] ["description"];
       $imagepathGB = urlencode($result["items"] [0] ["volumeInfo"] ["imageLinks"] ["thumbnail"]);
       $otherpathsGB =array();  
         $i = 1;
         while ($i <= ($items)) {
           $otherpathsGB[] = urlencode($result["items"] [$i] ["volumeInfo"] ["imageLinks"] ["thumbnail"]);
           $i++;
         }

return array($items, $title, $author, $descr, $imagepathGB, $otherpathsGB);

}

if (!isset($_GET["isbn"])) {
  die("INVALID URL");
} 

else {
  $isbn = $_GET["isbn"];
}

if ($isbn == '') {
   $isbn = "978x";
}

$array = google($isbn);

$descr = $array[3];

echo "isbn: ".$isbn."<BR>";
echo "items: ".$array[0]."<BR>";
echo "title: ".$array[1]."<BR>";
echo "author: ".$array[2]."<BR>";
echo "descr: <BR>".$descr."<BR>";
echo "encoding: <BR>".mb_detect_encoding($descr)."<BR>";
echo urldecode($array[4])."<BR>";
echo "otherpathsGB: <BR>";
echo var_dump($array[5]);

?>