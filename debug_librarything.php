<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

function librarything($isbn) {
  $ltrequest = "http://www.librarything.com/api/whatwork.php?isbn=".$isbn;
  $ltresponse = @file_get_contents($ltrequest);
  $pxml = simplexml_load_string($ltresponse);
  
  if ($pxml === FALSE) {
    $librarything_status=FALSE;
  } 
  else {
    if (isset($pxml->link)) {
          $link = $pxml->link."/covers";
          $librarything_status=TRUE;
        }
  }
  return array($isbn, $ltrequest, $ltresponse, $pxml, $link, $librarything_status);
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

$ltresults = librarything($isbn);

echo "ISBN: ".$ltresults[0]."<BR>";
echo "LTrequest: ".$ltresults[1]."<BR>";
echo "LTresponse: ".$ltresults[2]."<BR>";
echo "Pxml: ".var_dump($ltresults[3])."<BR>";
echo "Link: ".$ltresults[4]."<BR>";

?>