<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">

<?php

//Currency converter using Yahoo! finance

function currency($currency, $amount) {

$to     = 'CHF'; //to Swiss Franc
 
$url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $currency . $to .'=X';

$handle = @fopen($url, 'r');
if ($handle) {
    $result = fgets($handle, 4096);
    fclose($handle);
}

$array = array(explode(',',$result));
$rate =  (float)$array[0][1];
$swissprice = $rate*$amount;
return $swissprice;
 
}
 
//if (!isset($_GET["from"]))
//{
//die("PARAMETER 'currency' IS MISSING. Parameter id is from=".$from);
//}
//else
//{
//$from = $_GET["from"];
//}

//if (!isset($_GET["amount"]))
//{
//die("AMOUNT is MISSING. Parameter id is amount=");
//}
//else
//{
//$amount = $_GET["amount"];
//}


// $final = currency($currency, $amount);
// var_dump($final);


?>