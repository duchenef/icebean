<?php 
//$file="http://z3950.loc.gov:7090/voyager?version=1.1&operation=searchRetrieve&query=9780471615156&maximumRecords=1&recordPacking=xml&recordSchema=marcxml";
$file="http://experimental.worldcat.org/fast/1024387/marc21.xml";
$xml = simplexml_load_file($file);
$xml->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');

foreach( $xml->xpath('//foo:record') as $record ) {
  echo "record: <BR>";
  $record->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');
  foreach( $record->xpath('foo:datafield[@tag="111" or @tag="150" or @tag="151"]') as $datafield ) {
    switch($datafield['tag']) {
      case '111':
        echo "event: \n";
        break;
      case '150':
        echo "topical: \n";
        break;
      case '151':
        echo "geographic : \n";
        break;
    }
    $datafield->registerXPathNamespace('foo', 'http://www.loc.gov/MARC21/slim');
    foreach( $datafield->xpath('foo:subfield') as $sf ) {
      echo '   ', $sf['code'] . ': ' . $sf . "\n";
    }    
  }
}