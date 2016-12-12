<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php


// @@@@@@@@@@@@@@@@@@@@@@@@@
function goodreads($isbn) {

$goodreadsapikey = "CsM8Of4v9ps4KWPHNjnvpw";
$urlr = 'https://www.goodreads.com/book/isbn/'.$isbn.'?key='.$goodreadsapikey;

$GRresponse = @file_get_contents($urlr);

if ($GRresponse === FALSE) {
    //echo "Goodreads request failed.\n";
    $goodreads_status = "Goodreads request failed";}

else {
    // parse XML
    $pxml = new DOMDocument;
    $pxml->load($urlr);
    if ($pxml === FALSE) {
        $goodreads_status = "Response for Goodreads could not be parsed.\n";
        //echo "Response for Goodreads could not be parsed.\n";
    } 
    
    else {
        $goodreads_status = "Goodreads request was successful.";

        $searchNode = $pxml->getElementsByTagName( "book" );
        $first = true; 

	foreach( $searchNode as $searchNode ) { 
            if ( $first ) { 
               $xmltitle = $searchNode->getElementsByTagName( "title" ); 
                  $title = $xmltitle->item(0)->nodeValue; 
               $xmldescription = $searchNode->getElementsByTagName( "description" ); 
                  $descr = $xmldescription->item(0)->nodeValue; 
               $xmlauthor = $searchNode->getElementsByTagName("author" );
               foreach ( $xmlauthor as $xmlauthor) {
                  $xmlname = $searchNode->getElementsByTagName("name" );
                  $author = $xmlname->item(0)->nodeValue;
               }
               $xmltitle = $searchNode->getElementsByTagName( "title" ); 
                  $title = $xmltitle->item(0)->nodeValue; 
               $xmlGRimagepath = $searchNode->getElementsByTagName( "image_url" ); 
                  $GRimagepath = $xmlGRimagepath->item(0)->nodeValue;  
               $first=false;
            }  
        } 
        
            
    }
}

  
return array($goodreads_status, $title, $author, $descr, $urlr, $pxml, $GRimagepath);

}


if (!isset($_GET["isbn"])) {
  die("INVALID URL");
}
else {
  $isbn = $_GET["isbn"];
}

$array = goodreads($isbn);

echo "isbn: ".$isbn."<BR>";
echo "<a href='".$array[4]."'>$array[4]</a><BR>";
echo "status: ".$array[0]."<BR>";
echo "title: ".$array[1]."<BR>";
echo "author: ".$array[2]."<BR>";
echo "descr: <BR>".$array[3]."<BR><BR>";
echo "image: ".$array[6]."<BR>";
echo "XML dump: <BR>";
echo var_dump($array[5]);


?>