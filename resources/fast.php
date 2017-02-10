<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

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
      // Field
      switch($datafield['tag']) {
        case '100':
          echo "600 (person) \n";
          $marcField .= '600';
          break;
        case '110':
          echo "610 (corporate name) \n";
          $marcField .= '610';
          break;
        case '11':
          echo "611 (event) \n";
          $marcField .= '611';
          break;
        case '130':
          echo "630 (title) \n";
          $marcField .= '630';
          break;
        case '150':
          echo "650 (subject) \n";
          $marcField .= '650';
          break;
        case '151':
          echo "651 (geographical) \n";
          $marcField .= '651';
          break;
      }
      $marcField .= chr(9);
      // Indicateurs
      $i1 = $datafield['ind1'];
      $marcField .= $i1;
          if ($i1 == ' ') {
            $i1 = '_';
          }
          echo $i1;
      $marcField .= chr(9);
      $i2 = '7';
      echo $i2;
      $marcField .= $i2;

      echo ' ';
      $marcField .= chr(10);

      // subfields
      $datafield->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');
      foreach( $datafield->xpath('foo:subfield') as $sf ) {
        echo ' ';
        echo '$', $sf['code'] . ' ' . $sf;
        $marcField .= $sf['code'].chr(9).$sf.chr(10);
        $tmpsf = $sf;
      }
    // Ponctuation
    if (substr($tmpsf, -1) != ')') {
      echo '.';
    }
    // subfield 2: value: fast
      echo ' ' ;
      echo  '$2 fast';
    }
    $marcField .= chr(0);
    echo ' ///';
    //echo $marcField;
  }
  echo "<BR>";
  $i++;
}

$marc_json = json_encode($marcField);
// verification
echo "-----------------------------------------------<BR>";
//echo "Url: ".$fldRequest."<BR>";
?>

<div id="script-target" >
  <?php 
          echo $marc_json; /* You have to escape because the result
                                             will not be valid HTML otherwise. */
      ?>
</div>



<script src="../js/clipboard.min.js">/* ne fonctionne pas parce que tab suivi d'espace est traite comme un seul charactere espace*/</script>

<button class="button" id="copy-button" data-clipboard-target="#script-target">Copy</button>


<script type="text/javascript">

    var div = document.getElementById("script-target");
    var myData = JSON.parse(div.textContent);
  
/*(function(){
  new Clipboard('#copy-button');*/

new Clipboard('.button', {
    text: function(trigger) {
        return myData;
    }
});

var a = Clipboard.isSupported()
console.log('Clipboard is supported: '+a)

</script>


