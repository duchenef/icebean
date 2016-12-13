<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

function amazon($isbn, $loc) {

// needs external function to build a signed request
  include('resources/aws_signed_request.php');
  include('resources/currency_converter.php');

// Amazon account IDs
  $public_key = 'AKIAJT44IZLM4IYIJ7TA';
  $private_key = 'T9J1JC2f1tGBAK8s5V2B1hVwwzwMsRSwbW3ThYWi';
  $associate_tag = 'httplibraries-21';
  $responseGroup = "Large";
  $imagepathAM = [];
  $format = [];

// Generate signed URL
$request = aws_signed_request($loc, array(
        'Operation' => 'ItemLookup',
        'SearchIndex' => 'Books',
	'IdType' => 'ISBN',
        'ItemId' => $isbn,
        'ResponseGroup' => $responseGroup), $public_key, $private_key, $associate_tag);

// do request

$response = @file_get_contents($request);

if ($response === FALSE) {
  $AmRequest_status="Amazon request failed";
}
else {
  $AmRequest_status="Amazon request was successful";
    
// parse XML
  $pxml = simplexml_load_string($response);
  if ($pxml === FALSE) {
    $AmRequest_status="Amazon request was successful, but response could not be parsed (simplexml_load_string failed)";
  } 
  else {
    for ($i=0; $i<3; $i++) {
      if(isset($pxml->Items->Item[$i]->LargeImage->URL) && $imagepathAM[$i]=='') {
        array_push($imagepathAM, $pxml->Items->Item[$i]->LargeImage->URL);
      }
      if (isset($pxml->Items->Item[$i]->ASIN) && !isset($asin) && $asin=='') {
        $asin=$pxml->Items->Item[$i]->ASIN;
      }
      if (isset($pxml->Items->Item[$i]->ItemAttributes->Title) && $title=='') {
        $title=$pxml->Items->Item[$i]->ItemAttributes->Title;
      }
      if (isset($pxml->Items->Item[$i]->ItemAttributes->Author) && $Author=='') {
        $author=$pxml->Items->Item[$i]->ItemAttributes->Author;
      }
      if (isset($pxml->Items->Item[$i]->ItemAttributes->Languages->Language->Name) && $language=='') {
        $language = $pxml->Items->Item[$i]->ItemAttributes->Languages->Language->Name;
      }
      if (isset($pxml->Items->Item[$i]->EditorialReviews->EditorialReview[0]->Content) && $reviewa=='') {
        $reviewa=$pxml->Items->Item[$i]->EditorialReviews->EditorialReview[0]->Content;
      }
      if (isset($pxml->Items->Item[$i]->EditorialReviews->EditorialReview[1]->Content) && $reviewb=='') {
        $reviewb=$pxml->Items->Item[$i]->EditorialReviews ->EditorialReview[1]->Content;
      }
      if (isset($pxml->Items->Item[$i]->ItemAttributes->Publisher)&& $publisher=='') {
        $publisher = $pxml->Items->Item[$i]->ItemAttributes->Publisher;
      }
      if (isset($pxml->Items->Item[$i]->ItemAttributes->NumberOfPages)&& $pages=='') {
        $pages = $pxml->Items->Item[$i]->ItemAttributes->NumberOfPages;
      }
      
      if (isset($pxml->Items->Item[$i]->ItemAttributes->PackageDimensions->Length)&& $heightinches=='') {
        $heightinches = $pxml->Items->Item[$i]->ItemAttributes->PackageDimensions->Length;
        $heightcm = round($heightinches * 0.0254);
      }
      if (isset($pxml->Items->Item[$i]->ItemAttributes->PublicationDate)&& $publicationdate=='') {
        $publicationdate = $pxml->Items->Item[$i]->ItemAttributes->PublicationDate;
      }
      if (isset($pxml->Items->Item[$i]->DetailPageURL)&& $pageurl=='') {
        $pageurl = $pxml->Items->Item[$i]->DetailPageURL;
      }
      #Pass if format is Kindle / avoid ebook price
      array_push($format, $pxml->Items->Item[$i]->ItemAttributes->Format." (".$i.")");
        if (strpos($format[$i], 'Kindle') !== false) {
          continue;
        }
      if ($formattedprice =='') {
        if (isset($pxml->Items->Item[$i]->ItemAttributes->ListPrice->FormattedPrice)&& $formattedprice=='') {
          $formattedprice = $pxml->Items->Item[$i]->ItemAttributes->ListPrice->FormattedPrice;
        }
        #if price not indicated in ItemAttribues, try first Offer in Offers
        else {
          $formattedprice = $pxml->Items->Item[$i]->Offers->Offer[0]->OfferListing->Price->FormattedPrice;
        }
      }
    }

    switch ($loc) {
      case 'fr':
        $currency = 'EUR';
        $amount = str_replace(",", ".", substr($formattedprice, 4, 20));
        break;
      case 'co.uk':
        $currency = 'GBP';
        $amount = substr($formattedprice, 2, 20);
        break;
      case 'com':
        $currency = 'USD';
        $amount = substr($formattedprice, 1, 20);
    }

    $swissprice = round(currency($currency, $amount) * 2, 1)/2;

    // Messages d'erreur si rien n'a été trouvé dans aucun des items
    if($imagepathAM[0]=='') {$imagepathAM[0]="no image found";}
    if($title=='') {$title="title not found";}
    if($asin=='') {$asin="ASIN not found";}
    if($language=='') {$language="language not found";}
    if($reviewa=='') {$reviewa="review 1 not found";}
    if($reviewb=='') {$reviewb="review 2 not found";}
    if($formattedprice=='') {
      $formattedprice="formatted price not found";
      $swissprice="swiss price could not be calculated";
    }
    if($publisher=='') {$publisher="publisher not found";}
    if($pages=='') {$pages="number of pages not found";}
    if($heightinches=='') {
      $heightinches="height not found";
      $heightcm="height in cm could not be calculated";
    }
    if($publicationdate=='') {$publicationdate="publication date not found";}
  }
}

return array($request, $pxml, $AmRequest_status, $imagepathAM, $asin, $language, $reviewa, $reviewb, $formattedprice, $swissprice, $publisher, $pages, $heightinches, $heightcm, $publicationdate, $title, $amount, $currency, $author, $format);

}

if (!isset($_GET["isbn"]))
{
die("ISBN PARAMETER WAS MISSING. Parameter id is isbn= ");
}
else
{
$isbn = $_GET["isbn"];
}

if (!isset($_GET["reg"]))
{
die("AMAZON REGION PARAMETER WAS MISSING. Parameter id is reg=, valid options are fr. co.uk. com");
}
else
{
$loc = $_GET["reg"];
}

$amazon = amazon($isbn, $loc);

echo "<b>\$isbn :</b><BR> ".$isbn."<BR>";
echo "<b>\$loc :</b><BR> ".$loc."<BR>";
echo "<b>\$request ([0]) :</b><BR> ".$amazon[0]."<BR>";
echo "<b>\$AmRequest_status ([2]) :</b><BR> ".$amazon[2]."<BR>";

$imagepathAM=$amazon[3];
echo "\$imagepathAM ([3]):<BR>";
echo gettype($imagepathAM);
echo "<BR>";
for($i=0, $c=count($imagepathAM); $i<=$c; $i++) {
  echo $imagepathAM[$i];
  echo "<BR>";
}

echo "<b>\$asin ([4]) :</b><BR> ".$amazon[4]."<BR>";
echo "<b>\$language ([5]) :</b><BR> ".$amazon[5]."<BR>";
echo "<b>\$reviewa ([6]) :</b><BR> ".$amazon[6]."<BR>";
echo "<b>\$reviewb ([7]) :</b><BR> ".$amazon[7]."<BR>";
echo "<b>\$formattedprice ([8]) :</b><BR> ".$amazon[8]."<BR>";
echo "<b>\$amount ([16]) :</b><BR> ".$amazon[16]."<BR>";
echo "<b>\$currency ([17]) :</b><BR> ".$amazon[17]."<BR>";
echo "<b>\$swissprice ([9]) :</b><BR> ".$amazon[9]."<BR>";
echo "<b>\$publisher ([10]) :</b><BR> ".$amazon[10]."<BR>";
echo "<b>\$pages ([11]) :</b><BR> ".$amazon[11]."<BR>";
echo "<b>\$heightinches ([12]) :</b><BR> ".$amazon[12]."<BR>";
echo "<b>\$heightcm ([13]) :</b><BR> ".$amazon[13]."<BR>";
echo "<b>\$publicationdate ([14]) :</b><BR> ".$amazon[14]."<BR>";
echo "<b>\$title ([15]) :</b><BR> ".$amazon[15]."<BR>";
echo "<b>\$author ([18]) :</b><BR> ".$amazon[18]."<BR>";
echo "<b>\$format ([19]) :</b><BR> ".$amazon[19][0]."/".$amazon[19][1]."/".$amazon[19][2]."<BR>";