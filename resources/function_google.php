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

?>