<?php

// Force download of text file specified in URL query string and which
// is in the same directory as this script:

if(!empty($_GET['txt']))
{
   $filename = basename($_GET['txt']); // don't accept other directories
     $fp = @fopen($filename, "rb");
      header("Content-type: text/plain");
      header("Content-Disposition: attachment; filename=$filename");
     fpassthru($fp);
      exit;
}
header("HTTP/1.0 404 Not Found");
?> 

