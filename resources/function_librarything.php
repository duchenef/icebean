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

?>