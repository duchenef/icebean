<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php


function classify($isbn) {

$clrequest = "http://classify.oclc.org/classify2/Classify?isbn=".$isbn;
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

?>