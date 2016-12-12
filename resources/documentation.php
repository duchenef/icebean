<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<title>IceBean Documentation</title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<style>
version
{
position:absolute;
left:600px;
top:26px;
}
body
{
background-color:#ddeeff;
font-family:"Verdana";
}
small
{
font-family:"Verdana";
font-size:12px;
}
verysmalli
{
font-family:"Verdana";
font-size:9px;
font-style:italic;

}
table, td, th
{
border-collapse:collapse;
border:1px solid #002277;
}
th
{
align
background-color:#bbddff;
background-image:url('ice.png');
color:#002255;
}
</style>
</head>
    
<version><verysmalli>the Ice Bean Documentation v1.3 20161012</verysmalli></version>

<table width = '1024'>
  <TR height='38px'>
        <TH align='left'><img align='middle' src='icebean2.png'></TH>
  </TR>
  <TR>
    <TH align='center'> Documentation </TH>   
  </TR>
  <TR>
    <TD>
<small>
<p><b>Presentation:</b></p>

<p>The Ice Bean is an ISBN search tool that queries multiple websites, trying to retrieve cover images and other book-related information. Pictures are  resized so they can be added to a library catalogue (field 996#a in Mandarin or other).</p>  
<p>The input must be an ISBN. It can be typed or scanned in 'ISBN 10 or 13' form field. The application can read ISBNs 10 and 13, EAN and even Amazon ASIN numbers. Nevertheless, depending on how the information is stored in each database, searching for an ISBN 10 or 13 does not alway return exactly the same result. A good recommendation is to search for the ISBN as it's printed on the actual book. It is also useful to sometimes search using the old ISBN 10 for reprinted editions: even if only the modern 13-character ISBN is printed on the book, as the old ISBN 10 is likely to be the one to be stored in some databases. If it's unknown, the ISBN 10 is automatically calculated from the ISBN 13 and shown in the 'Book details' section of the Icebean.</p>

<p><b>Main features:</b></p>

<p>1. Searches the given ISBN's book on <b>Google books</b>, <b>Amazon</b>, <b>Librarything</b>, <b>Open Library</b>, and <b>OCLC Classify</b> (optional).<BR>
2. Displays and resizes cover images that may be available from these websites.<BR>
3. Displays item information with Marc reference for the following fields (when available):</p>
<LI>Title
<LI>ISBN
<LI>Language
<LI>Author
<LI>Publisher
<LI>Date of publication
<LI>Number of pages
<LI>Dimensions
<LI>Dewey number and Dewey edition
<LI>Price (converted to CHF)
<LI>Summary (description)
<LI>FAST subject Headings
</LI>

<p><b>Other Features:</b></p>
<p>- The siZeBean is a tool that can resize JPEG pictures, using either an URL or by uploading a file to the server.<BR>
- Pictures are ILS-ready: click on save and you'll get a jpeg file of the requested dimension and named from the book's name and ISBN.<BR>
- Descriptions can be saved too (as text files).<BR>
- Searchable Library Of Congress relator terms, with codes.<BR>
- Searchable FAST heading engine with indication of the facets.<BR>
- Ctrl+Click the links in the Expand search area to search for the ISBN in Librarything, Classify, Worldcat, Amazon, BNF. It is also possible to search Nelligan using the author's name.</p> 

<p><b>Options:</b></p>
<p>- Default resize height is 120, best for Mandarin, but you can change it.<BR>
- Search Amazon.fr (default) or us or uk (doesn't make much difference apparently).<BR>
- If available, Amazon description can be preferred to Google's (for legal reasons, the Amazon descriptions you can see on their websites may not be shown here).<BR></p>

<p>- Please ignore the 'buy from Amazon' button: this button is compulsory for anyone who uses Amazon APIs.</p>
</small>
  </TD>  
  </TR>
  <TR>
    <TH align='center'>
      <FORM NAME="back" ACTION="documentation.htm" METHOD="POST">
      <input type='button' value='back to the ICE BEAN' onClick=window.location.href='../index.php'></form>
    </TH>
  </TR>
</table><br>

<table width = '1024'>
  <TR>
    <TH align='center'>Revision History</TH>
  </TR>
  <TR>
    <TD><verysmalli>

<?php
    $myfilename = "history.txt";
    if(file_exists($myfilename)){
      echo file_get_contents($myfilename);
    }
?>

</verysmalli></TD></TR></TABLE>
</body>
</html>