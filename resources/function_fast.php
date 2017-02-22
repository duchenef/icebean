<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<?php
function fast2mdr($isbn) {

  // Requete Classify
  // retourne un tableau: [0]=status, [1]=dewey, [2]=edition ddc, [3]=tableau contenant les FAST, [4]= tableau contenant les IDs des FAST
  include 'function_classify.php';

  $classify = classify($isbn);

  // recuperation des FASTs
  $classify_status = (string)$classify[0];
  $dewey = (string)$classify[1];
  $ddced = (string)$classify[2];
  $fastClassify = $classify[3];
  $fastID = $classify[4];
  $i=0;
  $marcArray =[];
  $readArray =[];

// si classify ne retourne pas de fast, ne rien executer
if ($fastClassify !='not found') {
  while ($i<=(count($fastClassify))-1) {
    //echo $fast[$i]." / ".$fastID[$i]."<BR>";

    // Requete FAST linked data
    $fldRequest = "http://experimental.worldcat.org/fast/".$fastID[$i]."/marc21.xml";
    $xml = simplexml_load_file($fldRequest);
    $xml->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');

    foreach( $xml->xpath('//foo:record') as $record ) {
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
          case '111':
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
    }
    $i++;
  }
}
return array($marcArray, $readArray, $classify_status, $dewey, $ddced);
}
?>