<!DOCTYPE html>
<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
    <title>IceBean</title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/icebean.css">
    
	<script src="js/jquery-1.5.2.min.js"></script>
	<script src="js/assignFASTPage.js" type="text/javascript"></script>
	<script src="js/assignFASTExample.js" type="text/javascript"></script>
	<script src="js/assignFASTGadget.js" type="text/javascript"></script>
	<script src="js/assignFASTComplete.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript" ></script>
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" > 

</head><link rel="stylesheet" type="text/css" href="data:text/css,">
    
<body onload="document.forms.main_form.isbn.focus(); setUpExamplePage();setUpPage()">

<version><verysmalli>the Ice Bean v4.67 20161213fd</verysmalli></version>

<?php

// nettoyage des caches sur le serveur (images et descriptions)
    //Repertoire images
    $imagesdir = "images/";
    // Pour chaque fichier du repertoire
    foreach(glob($imagesdir.'*.jpg') as $file){
    // Test d'anciennete: 24 heures = 86400 secondes
    if (filemtime($file) < time() - 86400) {
        unlink($file);
        }
    }

    //Repertoire descriptions
    $descrdir = "descriptions/";
    foreach(glob($descrdir.'*.txt') as $file){
    // Test d'anciennete: 24 heures = 86400 secondes
    if (filemtime($file) < time() - 86400) {
        unlink($file);
        }
    }

// recuperation de la region Amazon dans le formulaire
if (isset($_POST["Amregion"]))
{
$Amregion = $_POST["Amregion"];
$Amregion_status="Selected Amazon region: ".$Amregion;
}   
else
{
$Amregion = "fr";
$Amregion_status="no Amazon region provided, default is: ";
}

// recuperation de l'isbn dans le formulaire
  // pre-traitement
  $isbn = urlencode($_POST["isbn"]);
    $remove = array(" ", "-", "_", "\"");
    $isbn = str_replace($remove, '', $isbn);
  $isbn_status = "ISBN-like number found, special signs were removed: ";

if (isset($isbn)) //active les ASIN
// if (isset($isbn) and ctype_digit(substr($isbn,0,-1)))

{
  $isbn = urlencode($_POST["isbn"]); 
    $remove = array(" ", "-", "_");
    $isbn = str_replace($remove, '', $isbn);
  $isbn_status = "ISBN-like number found, special characters were removed: ";


// ISBN 13 to 10
function ISBN13toISBN10($isbn) {
    if (preg_match('/^\d{3}(\d{9})\d$/', $isbn, $m)) {
        $sequence = $m[1];
        $sum = 0;
        $mul = 10;
        for ($i = 0; $i < 9; $i++) {
            $sum = $sum + ($mul * (int) $sequence{$i});
            $mul--;
        }
        $mod = 11 - ($sum%11);
        if ($mod == 10) {
            $mod = "X";
        }
        else if ($mod == 11) {
            $mod = 0;
        }
        $isbn = $sequence.$mod;
    }
    return $isbn;
}

$isbn10 = ISBN13toISBN10($isbn);

  
// requete Google books
// retourne un tableau: [0]=items, [1]=title, [2]=author, [3]=description, [4]=image URL, [5]= tableau contenant les autres url

include 'resources/function_google.php';
if ($isbn == '') {$gisbn = "978";}
else {$gisbn = $isbn;}
$arrayGB = google($gisbn);

$items=$arrayGB[0];
$gbtitle = $arrayGB[1];
  if ($gbtitle == '') {
     $gbtitle_status = "Google Books Title: not found";
  }
  else { $gbtitle_status = "Google Books Title: ".$gbtitle; }
  $title = $gbtitle;
$author = $arrayGB[2];
$author_status = "author taken from Google";
$descrurlgb = $arrayGB[3];
$imagepathGB = $arrayGB[4];
$otherpathsGB = $arrayGB[5];

// Requete Amazon images avec ASIN titre
// retourne un tableau: [0]= request, [1]= xml brut, [2]= request status, [3]= images urls (array), [4]= $asin, [5]= $language, [6]= $reviewa, [7]= $reviewb, [8]= formatted price, [9]= swissprice, [10]= publisher, [11]= pages, [12]= dimensions inches, [13]= dimensions cm, [14]= publication date, [15]= title, [16]= detailed amazon page url , [17]= $review_status, [18]= author

include 'resources/function_amazon.php';

$amazon=amazon($isbn, $Amregion);

$AmRequest_status = $amazon[2];
$asin=$amazon[4];
$language=$amazon[5];
$amreviewa=$amazon[6];
$amreviewb=$amazon[7];
$descrurlam = "Description 1. ".$amreviewa." Description 2. ".$amreviewb;
$formattedprice=$amazon[8];
$swissprice="p".$amazon[9]."chf";
$publisher=$amazon[10];
$pages=$amazon[11];
$heightinches=$amazon[12];
$heightcm=$amazon[13];
$publicationdate=$amazon[14];
$pageurl=$amazon[16];
// Utiliser le titre amazon par defaut, sauf si vide
if($amazon[15]!='') {$title = $amazon[15];}
$review_status=$amazon[17];
if ($author == '') {
   $author= $amazon[18];
   $author_status = "Author taken from Amazon";
}

$arrayAMimages=$amazon[3];
$imagepathAM=$arrayAMimages[0];
$imagepathAM=urlencode($imagepathAM);


// post-traitement physical description amazon
// essai de formattage de champ mandarin
// $rdapd = "|".chr(30).chr(9)."300"."&#9".chr(32)."&#9".chr(32)."<BR>"."a".chr(9).$pages." pages ;"."<BR>"."c".chr(9).$heightcm."cm.|<BR>";

for ($i=1, $c=count($arrayAMimages); $i<=$c; $i++) {
  $arrayAMimages[0]=urlencode($imagepathAM[$i]);
}

// Requete Goodreads
// retourne un tableau: [0]=status, [1]=title, [2]=author, [3]=description, [4]=url de la requete, [5]=donnees brutes xml, [6]=chemin image goodreads
  include 'resources/function_goodreads.php';
  $goodreads = goodreads($isbn);
  
  $GRstatus = $goodreads[0];
  $GRtitle = $goodreads[1];
  $GRauthor = $goodreads[2];
  $GRdescr = $goodreads[3];
  $imagepathGR = $goodreads[6];
  

// Requete Classify
// retourne un tableau: [0]=status, [1]=dewey, [2]=edition ddc, [3]=tableau contenant les FAST

  include 'resources/function_classify.php';
  $classify = classify($isbn);
  $classify_status = (string)$classify[0];
  $dewey = (string)$classify[1];
  $ddced = (string)$classify[2];
  $fast = $classify[3];

// definition des chemins vers les images de librarything et open library
  // Librarything
  $imagepathLT = urlencode("http://covers.librarything.com/devkey/965ffc4c5a2309a2e686d65539230b15/large/isbn/".$isbn);
  // Open Library
  $imagepathOL = urlencode("http://covers.openlibrary.org/b/isbn/".$isbn."-L.jpg");

// definition des noms de fichier et chemins pour retrouver et telecharger ce que resizer.php va sauvegarder sur le serveur
    // construction du nom de fichier
    $filename = $title.'-'.$isbn;
       // nettoyage pour compatibilite url
       $filename = str_replace(' ', '_', $filename);
       $filename = str_replace('\'', '', $filename);
       $filename = str_replace('/', '', $filename);
       $filename = str_replace(',', '_', $filename);
       $filename = str_replace('&', 'and', $filename);
       $filename = str_replace('?', '', $filename);
       $filename = str_replace('(', '', $filename);
       $filename = str_replace(')', '', $filename);
       $filename = str_replace("\"", '', $filename);
       $filename = str_replace("#", '', $filename);
       $filename = str_replace(":", '', $filename);
       $filename = str_replace(";", '', $filename);
    // construction de l'adresse d'envoi de chaque fichier vers le downloader
    $filenamelt = $filename.'-lt.jpg';
    $picurllt = "images/download.php?img=$filenamelt";
    $filenameol = $filename.'-ol.jpg';
    $picurlol = "images/download.php?img=$filenameol";
    $filenamegb = $filename.'-gb.jpg';
    $picurlgb = "images/download.php?img=$filenamegb";
    $filenameam = $filename.'-am.jpg';
    $picurlam = "images/download.php?img=$filenameam";
    $filenamegr = $filename.'-gr.jpg';
    $picurlgr = "images/download.php?img=$filenamegr";

// choix de la description à afficher en fonction du choix dansle formulaire
    
   if ($_POST["Descr"] == 'amazon') {
   $descr = $descrurlam;
   $descr_status = "Selected description mode: Amazon";
   
   }
   elseif ($_POST["Descr"] == 'google') {
   $descr = $descrurlgb;
   $descr_status = "Selected description mode: Google";
   } 
   elseif ($_POST["Descr"] == 'goodreads'){
   $descr = $GRdescr;
   $descr_status = "Selected description mode: Goodreads (default)";
   } 


   $descr = str_replace("\xE2\x80\x99", "'", $descr);
   $descr = str_replace("\xE2\x80\xA6", "...", $descr); 
   $descr = "|".chr(30).chr(9)."520".chr(9)."8".chr(9).chr(32)."<BR>".chr(97).chr(9).$descr."|";

// definition du nom de fichier et creation du fichier contenant la description google books;
    $filename_descr = $filename.'.txt';
    $descrfileurl = "descriptions/download.php?txt=$filename_descr";
    $FileHandle = fopen("descriptions/".$filename_descr, 'w') or die("Can't open file: $filename");
    fwrite($FileHandle, $descr);
    fclose($ourFileHandle);

}   

else
{
$isbn_status = "no ISBN-like value was found";
}

// recuperer la valeur du champ height et verifier qu'il existe et qu'il s'agisse bien d'un nombre;
// agir en fonction;

if (isset($_POST["nh"]) and ctype_digit($_POST["nh"]))
{
$nh = $_POST["nh"];
$nh_status = "Height value (for picture resizing): ";
} 
  
else
{
$nh_status = "Default H value was used: ";
$nh = "120";
}

?>

<table width = '1280'>
  <TR height='38px'>
        <TH align='left' colspan='6'><img align='middle' src='resources/icebean2.png'></TH>
        <TH align='center' colspan='2'>
          <FORM NAME="sizebean" ACTION="sizebean.php" METHOD="POST">
	  <button class='button' id='sizebean' onClick=window.location.href='sizebean.php'>siZeBean</button>
           </form>
        </TH>
  </TR>
  <TR>
    <TD colspan='1'><FORM NAME="main_form" ACTION="index.php" METHOD="POST">
      <verysmalli>Picture height (in pixels) </verysmalli></td>
    <TD colspan='1'>
      <INPUT TYPE="integer"  NAME="nh" VALUE="<?PHP echo $nh; ?>" SIZE="1" MAXLENGTH="3"</TD>
    <TD colspan='1'>
      <verysmalli>Amazon region</verysmalli></td>
    <TD colspan='1'>
      <select name="Amregion">
	<option value="fr">France</option>
	<option value="co.uk">UK</option>
	<option value="com">US</option>
	</select>
    </TD>
    <TD colspan='1'>
      <verysmalli>Description from</verysmalli></td>
    <TD colspan='1'>
      <select name="Descr">
	<option value="goodreads">Goodreads</option>
	<option value="google">Google Books</option>
	<option value="amazon">Amazon</option>
	</select>
    </TD>
    <TD align='center' colspan='2'>
      <INPUT TYPE="text" id='isbn' NAME="isbn" placeholder="ISBN 10 or 13" VALUE="<?PHP echo $isbn; ?>" MAXLENGTH="13">
      <INPUT TYPE="submit" NAME="SEND">
    </TD>
  </TR>


<?php

// Affichage des resultats;
//Cover images

  echo "<tr>";
    echo "<th align='left' colspan='4'>Cover Images</th>";
    echo "<th align='left' colspan='4'>Title and Summary</th>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='1'><small>Librarything</small></td>";
//  echo "<td colspan='1'><small>Open Library</small></td>";
    echo "<td colspan='1'><small>Goodreads</small></td>";
    echo "<td colspan='1'><small>Google books</small></td>";
    echo "<td colspan='1'><small>Amazon</small></td>";
    echo "<td colspan='1'><small>Title (245#ab)</small></td>";
    echo "<td align='right' colspan='3'><small>$title</small></td>";
    
  echo "</tr>";
  echo "<tr style='height:136px'>";
    echo "<td colspan='1'><img align='middle' src='resources/resizer.php?url=".$imagepathLT."&h=".$nh."&fn=".$filenamelt."'></td>";
//  echo "<td colspan='1'><img align='middle' src='resources/resizer.php?url=".$imagepathOL."&h=".$nh."&fn=".$filenameol."'></td>";
    echo "<td colspan='1'><img align='middle' src='resources/resizer.php?url=".$imagepathGR."&h=".$nh."&fn=".$filenamegr."'></td>";
    echo "<td colspan='1'><img align='middle' src='resources/resizer.php?url=".$imagepathGB."&h=".$nh."&fn=".$filenamegb."'></td>";
    echo "<td colspan='1'><img align='middle' src='resources/resizer.php?url=".$imagepathAM."&h=".$nh."&fn=".$filenameam."'></td>";
    echo "<td colspan='4 align='right'><verysmalli>$descr</verysmalli></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='1'><form><input type='button' value='save' onClick=\"window.location.href='$picurllt'\"></form></td>";
//  echo "<td colspan='1'><form><input type='button' value='save' onClick=\"window.location.href='$picurlol'\"></form></td>";
    echo "<td colspan='1'><form><input type='button' value='save' onClick=\"window.location.href='$picurlgr'\"></form></td>";
    echo "<td colspan='1'><form><input type='button' value='save' onClick=\"window.location.href='$picurlgb'\"></form></td>";
    echo "<td colspan='1'><form><input type='button' value='save' onClick=\"window.location.href='$picurlam'\"></form></td>";
    echo "<td colspan='2'><small> Summary (520#a) </small></td>";
    echo "<td colspan='1'><form><input type='button' value='save' onClick=\"window.location.href='$descrfileurl'\"></form></td>";
    echo "<td colspan='1'><verysmalli>Amazon status: $review_status  </verysmalli></td>";
  echo "</tr>";

echo "</center></td></TR>";

// Book details
  echo "<tr>";
    echo "<th align='left' colspan='4'>Book details</th>";
    echo "<th align='left' colspan='4'>Fast subject headings (6xx)</th>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='1' align='left'><small>ISBN (020#a)</small></td>";
    echo "<td colspan='1' align='right'><small>$isbn</small></td>";
    echo "<td colspan='1' align='right'><small>ISBN 10: </small></td>";
    echo "<td colspan='1' align='right'><small>$isbn10</small></td>";
    echo "<td colspan='4' rowspan='9'align='right'><small>";
        // Classify (6xx)
        // Affichage des fast dans la colonne de droite
        echo $fast[0];
        if ($fast[0] ='') {
          echo "ot found";
        }
        $i=1;
        while ($i<=(count($fast))-1) {
          echo "<br>";
          echo $fast[$i];
          $i++;
        }
    echo "</small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' width='25%' align='left'><small>Language (041#a)</small></td>";
    echo "<td colspan='2' width='25%' align='right'><small>$language</small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' align='left'><small>Author (100#a)</small></td>";
    echo "<td colspan='2' align='right'><small>$author  </small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' align='left'><small>Publisher (264#b)</small></td>";
    echo "<td colspan='2' align='right'><small>$publisher  </small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' align='left'><small> Date of publication (264#c)</small></td>";
    echo "<td colspan='2' align='right'><small>$publicationdate  </small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' align='left'><small>Extent (Number of pages) (300#a)</small></td>";
    echo "<td colspan='2' align='right'><small>$pages pages  </small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' align='left'><small>Dimensions (300#c)</small></td>";
    echo "<td colspan='2' align='right'><small>$heightcm cm  </small></td>";
  echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2' align='left'><small>Most frequent Dewey number (edition)</small></td>";
    echo "<td colspan='2' align='right'><small>$dewey ($ddced)  </small></td>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='2' align='left'><small>Price (9) [$formattedprice = ]  </small></td>";
    echo "<td colspan='2' align='right'><small>$swissprice  </small></td>";
  echo "</tr>";

// Tools
  echo "<tr>";
    echo "<th align='left' colspan='8'>Tools</th>";
  echo "</tr>";
  echo "<tr>";
  // Relator terms
    echo "<td colspan='2'><input type='text' id='ajax' list='json-datalist' placeholder='type'>";
    echo "<datalist id='json-datalist'></datalist></td>";

  // Language codes
    echo "<td colspan='2'><input type='text' id='lng' list='json-lng' placeholder='type'>";
    echo "<datalist id='json-lng'></datalist></td>";

  // FAST headings
    echo "<td colspan='4'>";
    echo "<form id='dummySearchForm' class='dummy'  action=''>";
    echo "<div>";
    echo "<input id='examplebox'  type='text' placeholder='Fast headings' size='60'></input>";
    echo "<small><span id='exampleXtra'>&nbsp;<small></span>";
    echo "</div></form></td>";

  echo "</tr>";

// Expand search
  echo "<tr>";
    echo "<th align='left' colspan='8'>Expand search</th>";
  echo "</tr>";
  echo "<tr>";
    echo "<td colspan='1'><verysmalli><a href='http://covers.librarything.com/isbn/".$isbn."'>LibraryThing</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='http://classify.oclc.org/classify2/ClassifyDemo?search-standnum-txt=".$isbn."'>OCLC Classify</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='http://www.worldcat.org/search?q=".$isbn."'>OCLC Worldcat</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='$pageurl'>Amazon</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='https://www.goodreads.com/book/isbn/".$isbn."'>Goodreads</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='http://catalogue.bnf.fr/rechercher.do?motRecherche=".$isbn."&critereRecherche=0&depart=0&facetteModifiee=ok'>BNF (ISBN13)</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='http://catalogue.bnf.fr/rechercher.do?motRecherche=".$isbn10."&critereRecherche=0&depart=0&facetteModifiee=ok'>BNF (ISBN10)</a></verysmalli></td>";
    echo "<td colspan='1'><verysmalli><a href='http://nelligan.ville.montreal.qc.ca/search*frc/a?searchtype=Y&searcharg=".urlencode($author)."&searchscope=58&extended=0&SORT=D&submit.x=0&submit.y=0&submit=Chercher'>Nelligan (search by author)</a></verysmalli></td>";

echo "</tr>";

// Output log
echo "<tr>";
  echo "<th align='left' colspan='8' >Output log </th></tr>";
  echo "<td colspan='8'><verysmalli>";
    echo $isbn_status.$isbn." (ASIN: ".$asin.") -- ".$nh_status.$nh." pixels -- ".$Amregion_status." -- ";

    if (!($descr_status == NULL)) {
      echo $descr_status." -- ";
    }

    if (!($gbtitle_status == NULL)) {
      echo $gbtitle_status." ";
    }

    if (!($amtitle_status == NULL)) {
      echo $amtitle_status." -- ";
    }

    if (!($descrurlam_status == NULL)) {
      echo $descrurlam_status." -- ";
    }

   echo " -- ".$author_status." -- ";

   echo $AmRequest_status." -- ".$classify_status."<BR>";


echo "</verysmalli></td></tr>";

echo "<tr align = 'right'>
  <td colspan='4'align='right'>";
     echo "<form method='GET' action='http://www.amazon.fr/gp/aws/cart/add.html'><input type='hidden' name='AssociateTag' value='".$associate_tag."'/>         <input type='hidden' name='SubscriptionId' value='".$public_key."'/><input type='hidden' name='ASIN.1' value='".$asin."'/><input type='hidden'           name='Quantity.1' value='1'/> <input type='image' name='add' value='Buy from Amazon.fr' border='0' alt='Buy from Amazon.fr'     align='middle' src='resources/amazon.gif'></form>";
  echo "</td>";

// Log de connexion;
   if (strlen($isbn) >= 10) {
    $hostname = $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $logtext = date(DATE_RFC822)." Hostname: ".$hostname." ISBN: ".$isbn." Title: ".$title."\r\n";
    $filename_log = 'resources/log.txt';
    $FileHandle = fopen($filename_log, 'a') or die("Can't open log.txt file");
    fwrite($FileHandle, $logtext);
    fclose($ourFileHandle);
   }

?>

<td align='right' colspan='4'><FORM NAME="back" ACTION="resources/documentation.php" METHOD="POST">
     <button class='button' value='Documentation' onClick=window.location.href='resources/documentation.php'>Help</button>
</td></form></tr>

</table>

</body>
</html>

<script>

// RELATOR TERMS AJAX
// Get the <datalist> and <input> elements.
var dataList = document.getElementById('json-datalist');
var input = document.getElementById('ajax');

// Create a new XMLHttpRequest.
var request = new XMLHttpRequest();

// Handle state changes for the request.
request.onreadystatechange = function(response) {
  if (request.readyState === 4) {
    if (request.status === 200) {
      // Parse the JSON
      var jsonOptions = JSON.parse(request.responseText);
  
      // Loop over the JSON array.
      jsonOptions.forEach(function(item) {
        // Create a new <option> element.
        var option = document.createElement('option');
        // Set the value using the item in the JSON array.
        option.value = item;
        // Add the <option> element to the <datalist>.
        dataList.appendChild(option);
      });
      
      // Update the placeholder text.
      input.placeholder = "LOC Relator terms";
    } else {
      // An error occured :(
      input.placeholder = "Couldn't load datalist options :(";
    }
  }
};

// Update the placeholder text.
input.placeholder = "Loading options...";

// Set up and make the request.
request.open('GET', 'resources/relatorterms.json', true);
request.send();

// LANGUAGE CODES AJAX
// Get the <datalist> and <input> elements.
var dataList_lng = document.getElementById('json-lng');
var input_lng = document.getElementById('lng');

// Create a new XMLHttpRequest.
var request_lng = new XMLHttpRequest();

// Handle state changes for the request.
request_lng.onreadystatechange = function(response) {
  if (request_lng.readyState === 4) {
    if (request_lng.status === 200) {
      // Parse the JSON
      var jsonOptions_lng = JSON.parse(request_lng.responseText);
  
      // Loop over the JSON array.
      jsonOptions_lng.forEach(function(item) {
        // Create a new <option> element.
        var option = document.createElement('option');
        // Set the value using the item in the JSON array.
        option.value = item;
        // Add the <option> element to the <datalist>.
        dataList_lng.appendChild(option);
      });
      
      // Update the placeholder text.
      input_lng.placeholder = "LOC Language codes";
    } else {
      // An error occured :(
      input_lng.placeholder = "Couldn't load datalist options :(";
    }
  }
};

// Update the placeholder text.
input_lng.placeholder = "Loading options...";

// Set up and make the request.
request_lng.open('GET', 'resources/languagecodes.json', true);
request_lng.send();

</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-12520299-1', 'auto');
  ga('send', 'pageview');

</script>