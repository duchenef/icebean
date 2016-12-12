<?php

// Force download of image file specified in URL query string and which
// is in the same directory as this script:

if(!empty($_GET['img']))
{
   $filename = basename($_GET['img']); // don't accept other directories
   $size = @getimagesize($filename);
   $fp = @fopen($filename, "rb");
   if ($size && $fp)
   {
      header("Content-type: {$size['mime']}");
      header("Content-Length: " . filesize($filename));
      header("Content-Disposition: attachment; filename=$filename");
      header('Content-Transfer-Encoding: binary');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      fpassthru($fp);
      exit;
   }
}
header("HTTP/1.0 404 Not Found");
?> 