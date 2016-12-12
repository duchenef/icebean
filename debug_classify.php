<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

function classify($isbn) {

$clrequest = "http://classify.oclc.org/classify2/Classify?isbn=".$isbn;
$clresponse = @file_get_contents($clrequest);
$fast=array();
echo "<a href='".$clrequest."'>$clrequest</a><BR>";

if ($clresponse === FALSE) {
    echo "Classify request failed.\n";
    $classify_status = "Classify request failed";}

else {
    // parse XML
    $pxml = new DOMDocument;
    $pxml->load($clrequest);
    if ($pxml === FALSE) {
        $classify_status = "Response for Classify could not be parsed.\n";
        echo "Response for Classify could not be parsed.\n";
    } 
    
    else {
        $classify_status = "Classify request was successful. Details (Dewey, Ed., FAST): ";
        echo var_dump($pxml)."END1<BR>"; 
        echo gettype($pxml)." END2<BR>";
        
        // Dewey (most popular) & Dewey Edition
        $ddc = $pxml->getElementsByTagName("ddc");
           foreach ($ddc as $ddc) { 
           $mostPopular = $ddc->getElementsByTagName("mostPopular");
             foreach ($mostPopular as $mostPopular) {
             $dewey = $mostPopular->getAttribute('nsfa');
             $dewey2 = $mostPopular->getAttribute('sf2');
             $classify_status = $classify_status.$dewey." , ";
             }
           }
        if ($dewey == NULL) {
           $dewey= "not found";
           $classify_status = $classify_status.$dewey." , ";
        }
        if ($dewey2 == NULL) {
           $dewey2= "not found";
           $classify_status = $classify_status.$dewey2." , ";
        }
        // Fast
        $headings = $pxml->getElementsByTagName("heading");
           foreach ($headings as $heading) { 
           $h= $heading->nodeValue;
           array_push($fast, (string)$h);
           }
           
        if ($fast == NULL) {
           $fast= "not found";
           $classify_status = $classify_status.$fast.".";
        }
    }
}

  if ($fast) {
       foreach ($fast as $value) {
       $faststr = $faststr.(string)$value." | ";
       }

  }

return array($classify_status, (string)$dewey, (string)$dewey2, $fast);

}

// --------------

if (!isset($_GET["isbn"]))
{
die("INVALID URL");
}
else
{
$isbn = $_GET["isbn"];
}

$classify = classify($isbn);
    $classify_status = (string)$classify[0];
    $dewey = (string)$classify[1];
    $ddced = (string)$classify[2];
    $fast = $classify[3];

echo $classify_status."<BR>";
echo $dewey."<BR>";
echo $ddced."<BR>";

print_r($classify[3])."<BR>";
echo reset($fast)."<BR>";
echo $fast[0]."<BR>";
echo $fast[1]."<BR>";
echo $fast[2]."<BR>";
echo $fast[3]."<BR>";
echo $fast[4]."<BR>";
echo $fast[5]."<BR>";
echo $fast[6]."<BR>";
echo $fast[7]."<BR>";


?>