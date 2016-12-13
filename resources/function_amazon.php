<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

function amazon($isbn, $loc) {

// needs external function to build a signed request
  include('aws_signed_request.php');
// needs external function to convert currency to CHF
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
    if($reviewa=='') {$reviewa="review 1 not found";$review_status="Not found";}
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
    if($pageurl=='') {$pageurl="Detailed page URL not found";}
  }
}

return array($request, $pxml, $AmRequest_status, $imagepathAM, $asin, $language, $reviewa, $reviewb, $formattedprice, $swissprice, $publisher, $pages, $heightinches, $heightcm, $publicationdate, $title, $pageurl, $review_status, $author, $format);

}

?>