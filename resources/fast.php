<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
    <title>Fast2Mdr</title>   
    <link rel="stylesheet" type="text/css" href="../css/fast.css">
</head>
<?php


// --------------
if (!isset($_GET["isbn"]))
{
die("INVALID URL");
}
else
{
$isbn = $_GET["isbn"];
}

//$isbn='9780789327482';

// Requete Classify
// retourne un tableau: [0]=status, [1]=dewey, [2]=edition ddc, [3]=tableau contenant les FAST
include 'function_classify.php';

$classify = classify($isbn);
$classify_status = (string)$classify[0];

// recuperation des FASTs
$fast = $classify[3];
$fastID = $classify[4];

// Display
echo "FAST retrieval from ISBN in MARC21";
echo "<BR>-----------------------------------------------<BR>";
$i=0;
$marcArray =[];
$readArray =[];

while ($i<=(count($fast))-1) {
  //echo $fast[$i]." / ".$fastID[$i]."<BR>";

  // Requete FAST linked data
  $fldRequest = "http://experimental.worldcat.org/fast/".$fastID[$i]."/marc21.xml";
  $xml = simplexml_load_file($fldRequest);
  $xml->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');

  foreach( $xml->xpath('//foo:record') as $record ) {
    //echo "record: <BR>";
    $record->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');
    foreach( $record->xpath('foo:datafield[@tag="100" or @tag="110" or @tag="111" or @tag="130" or @tag="150" or @tag="151"]') as $datafield ) {
      $marcField = chr(30).chr(9);
      $readField = ''; 
      // Field
      switch($datafield['tag']) {
        case '100':
          $marcField .= '600';
          $readField .= '600';
          break;
        case '110':
          $marcField .= '610';
          $readField .= '610';
          break;
        case '11':
          $marcField .= '611';
          $readField .= '611';
          break;
        case '130':
          $marcField .= '630';
          $readField .= '630';
          break;
        case '150':
          $marcField .= '650';
          $readField .= '650';
          break;
        case '151':
          $marcField .= '651';
          $readField .= '651';
          break;
      }
      $marcField .= chr(9);
      // Indicateurs
      $i1 = $datafield['ind1'];
      $marcField .= $i1;
          if ($i1 == ' ') {
            $i1 = '_';
          }
      $marcField .= chr(9);
      $i2 = '7';
      $marcField .= $i2;

      $marcField .= chr(10);
      $readField .= ' '.$i1.$i2.' ';

      // subfields
      $datafield->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');
      foreach( $datafield->xpath('foo:subfield') as $sf ) {
        $marcField .= $sf['code'].chr(9).$sf.chr(10);
        $readField .= '$'.$sf['code'].' '.$sf.' ';
        $tmpsf = $sf;
      }
    // Ponctuation
    if (substr($tmpsf, -1) != ')') {
      $marcField = substr_replace($marcField, '.', -1, 0);
      $readField = rtrim($readField).'. ';
    }
    // subfield 2: value: fast
      $marcField .= '2'.chr(9).'fast'.chr(10);
      $readField .= '$2 fast';

    }
    $marcField .= chr(0);
    array_push($marcArray, json_encode($marcField));
    array_push($readArray, $readField);
    //echo $readField;
    //echo "<BR>";
  }
  $i++;
}

// verification
//echo "Url: ".$fldRequest."<BR>";

// Affichage des resultats en html et caches pour javascript
$j = 0;

echo '<div id="fastwrapper">';
foreach ($marcArray as $value) {
  echo '<div class ="fast" id="fastdisplay'.$j.'">'.$readArray[$j].'</div>';
  echo '<button class="buttons" id="copy-button'.$j.'" data-clipboard-target="#fast'.$j.'">Copy '.$j.'</button>';
  echo '<div class ="hidden" id="fast'.$j.'" style="display: none;">'.$value.'</div>'; 
  $j++;
}
echo '</div>';
?>

<script src="../js/clipboard.min.js"></script>
<script type="text/javascript">
    /*var div1 = document.getElementById("fast1");
    var myData1 = JSON.parse(div1.textContent);
    var div2 = document.getElementById("fast2");
    var myData2 = JSON.parse(div2.textContent); 
    var div3 = document.getElementById("fast3");
    var myData3 = JSON.parse(div3.textContent); */  

    var divs = document.getElementsByClassName("hidden");
    var buttons = [];
    var mandarin = [];
    var i;
    for (i = 0; i < divs.length; i++) {
        console.log(i);
        mandarin.push(JSON.parse(divs[i].textContent));
        buttons.push('#'+document.getElementsByClassName('buttons')[i].id);
    } 

console.log(mandarin);
console.log(buttons);

new Clipboard('.buttons', {
            text: function(trigger) {
                console.log(JSON.parse(trigger.nextElementSibling.textContent));
                return JSON.parse(trigger.nextElementSibling.textContent);
            }
        });


/**
new Clipboard('#copy-button1', {
    text: function(trigger) {
        return myData1;
    }
});

new Clipboard('#copy-button2', {
    text: function(trigger) {
        return myData2;
    }
});

new Clipboard('#copy-button3', {
    text: function(trigger) {
        return myData3;
    }
});
*/

/*var a = Clipboard.isSupported()
console.log('Clipboard is supported: '+a)*/

</script>