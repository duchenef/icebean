<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

function classify($isbn) {

$clrequest_work = "http://classify.oclc.org/classify2/Classify?isbn=".$isbn."&summary=true";
$clresponse_work = @file_get_contents($clrequest_work);
$owis = [];

if ($clresponse_work === FALSE) {
    echo "Classify work number request failed.\n";
    $classify_status = "Classify work number request failed";}

else {
    // parse XML
    $pxml_w = new DOMDocument;
    $pxml_w ->load($clrequest_work);
    if ($pxml_w === FALSE) {
        $classify_status = "Response for Classify work number could not be parsed.\n";
        echo "Response for Classify work number could not be parsed.\n";
    } 
    
    else {
        $classify_status = "Classify work number request was successful. Details (Dewey, Ed., FAST): ";

        $work = $pxml_w->getElementsByTagName("work");
        foreach ($work as $work) {
          $x = $work->getAttribute('owi');
          array_push($owis, $x);
        }
    }
}

$clrequest = "http://classify.oclc.org/classify2/Classify?owi=".$owis[0];
$clresponse = @file_get_contents($clrequest);
$fast = array();
$fastID = array();

if ($clresponse === FALSE) {
    //echo "Classify request failed.\n";
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
        $heading = $pxml->getElementsByTagName("heading");
           foreach ($heading as $heading) {
           $h = $heading->nodeValue;
           array_push($fast, (string)$h);
           $id = $heading->getAttribute('ident');
           array_push($fastID, (string)$id);
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

// retourne un tableau: [0]=status, [1]=dewey, [2]=edition ddc, [3]=tableau contenant les FAST, [4]= tableau contenant les IDs des FAST
return array($classify_status, (string)$dewey, (string)$dewey2, $fast, $fastID);

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
    $fastID = $classify[4];

echo '0: '.$classify_status."<BR>";
echo '1: '.$dewey."<BR>";
echo '2: '.$ddced."<BR>";
echo '3: ';
print_r($classify[3])."<BR>";
//echo reset($fast)."<BR>";
echo '<BR>4: ';
print_r($classify[4])."<BR>";
echo '<BR><BR>FAST headings: <BR>';
echo $fast[0]."<BR>";
echo $fast[1]."<BR>";
echo $fast[2]."<BR>";
echo $fast[3]."<BR>";
echo $fast[4]."<BR>";
echo $fast[5]."<BR>";
echo $fast[6]."<BR>";
echo $fast[7]."<BR>";
echo $fast[8]."<BR>";

?>