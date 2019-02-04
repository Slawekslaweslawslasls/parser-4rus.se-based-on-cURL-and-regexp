<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
</head>
<?php
set_time_limit(0);
$fp = fopen("emails2.csv", "a");
$i=168407;//set initial point of search
$deep_search=6291;//set deep of search

for($start_deep=5616; $start_deep<$deep_search; $start_deep++){

$url[$start_deep]="http://www.sweden4rus.nu/rus/anons/poisksub?page=".$start_deep."&vremiaPE=ON";
echo "<b>".$url[$start_deep]."</b><br>";
$curl[$start_deep] = curl_init($url[$start_deep]);
//curl_setopt($curl, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($curl[$start_deep], CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl[$start_deep], CURLOPT_CONNECTTIMEOUT, 0); 
$html[$start_deep] = curl_exec($curl[$start_deep]);

$dom[$start_deep] = new DOMDocument();
@$dom[$start_deep]->loadHTML($html[$start_deep]);
$xpath[$start_deep] = new DOMXPath($dom[$start_deep]);
$tableRows[$start_deep] =$xpath[$start_deep]->query("//div[@class='PalmaAnons']/div[@class='PalmaAnonsHeader']");


foreach ($tableRows[$start_deep] as $value) {
	$i++;
	$nodes_iterator[$i]=$value->ownerDocument->saveHTML($value);
	//echo "nodes_iterator= ".$nodes_iterator;
	$line=$i.", ".get_id($nodes_iterator[$i]).", ".get_email($nodes_iterator[$i]).", ".get_phone($nodes_iterator[$i]).", ".get_date($nodes_iterator[$i]).", ".get_section($nodes_iterator[$i]);
	echo $line."<br>";
	fwrite($fp, $line.PHP_EOL);

}
}
unset($tableRows);




//$uagent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16";


//$regexp = '/<a href="announcement\?id=(.\d+)">.*<div class="PalmaAnonsHeaderInfo">(.*?)<img.*email.png">(.*)<\/div>.*Телефон:(.*?)<\/div>.*(.*г.).*Рубрика: <a class="infoW".*?(.*?)<\/a>/siu';

//var_dump($tableRows);
//print_r($tableRows);



function get_id($raw_id){
$regexp = '/<a href="announcement\?id=(.\d+)">/';
preg_match_all($regexp, $raw_id, $match,PREG_SET_ORDER);
return  $match[0][1];
}

function get_email($raw_email){
$regexp = '/.*<div class="PalmaAnonsHeaderInfo">(.*)<img.*email.png">(.*?)<\/div>.*/sui';
preg_match_all($regexp, $raw_email, $match,PREG_SET_ORDER);
return  trim($match[0][1])."@".trim($match[0][2]);
}
function get_phone($raw_phone){
$regexp = '/Телефон:(.\d+)/sui';
preg_match_all($regexp, $raw_phone, $match,PREG_SET_ORDER);
return $match[0][1];
}

function get_date($raw_date){
$regexp = '/(\d+ \w+ \d+ г.)/sui';
preg_match_all($regexp, $raw_date, $match,PREG_SET_ORDER);
return $match[0][1];
}

function get_section($raw_section){
$regexp = '/<a class="infoW".*>(.*)<\/a>/sui';
preg_match_all($regexp, $raw_section, $match,PREG_SET_ORDER);
return $match[0][1];
}
?>
</html>