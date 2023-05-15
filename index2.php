<?php

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * The routines here dispatch control to the appropriate handler, which then
 * prints the appropriate page.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 */

/**
 * Root directory of Drupal installation.
 */
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//menu_execute_active_handler();


//$prro = TPSPayRROAPI::get(2);
//file_put_contents('private://prro.cookies.txt', 'testccc');
//$prro = TPSPayPrro::get()->shiftOpen();
//$res = $prro->validateWorkingState(false);
//$res = $prro->receiptGetLast();
//$res = $prro->shiftClose();

module_load_include('inc','tps_retail','tps.resource');
//$res = _tpsrr_rro_get_logs();
$res = _tpsrr_close_day((object)array("uid"=>0));

var_dump($res);
//echo "res = ".$formatInfo;
//$prro->shiftClose();

//$prro->receiptDeleteActive(); die();
//$last = $prro->shiftGetCurrent(); print_r($last); die();
//$last = $prro->receiptGetLast(); print_r($last); die();
//$prro->shiftOpen();
//print_r(TPSPayRROAPI::get()->validateWorkingState(false));
//echo tps_pay_method_rro_cron();
//module_load_include('inc','tps_pay_method','tpspay.resource');
//_tpsrr_rro_receipt_check('1R8S+8');
//print_r($res);

die("уже не пользуемся этой штукой");

//load transaction and convert to trans object (so we can save it in future)
$trans = TPSTransAPI::read("1G3Q5", true);					//get full transaction info
if(!$trans) return 'Error: cant load transaction with given ID';
$trans = TPSTransValidator::castHashes($trans);					    //cast loaded data hash to transaction
print_r($trans);die();
TPSPayRRO::sendReceipt($trans);

echo "// end";


$csv = '';

/**
 * Загружает таблицу с сервера парсит каждый её ряд в массив и отправляет в спец. функцию
 **/
function tps_csv_update_price($city) {
	$fileName= 'https://docs.google.com/spreadsheets/d/1wW4P9TOJuo7XP_CpCSe4YnstlTL_hAW6-t2lBhuFeMU/export?gid=0&format=csv';
	$csvData = file_get_contents($fileName);
	$lines = explode(PHP_EOL, $csvData);
	$array = array();
	foreach ($lines as $line) {
	    $lineArr = str_getcsv($line);
	    $res = tps_csv_apply_product_row($lineArr, $city);
	    if($res) echo $res."<br>";
	}
}

/**
 * Принимает массив данных одной строки из базы товаров
 * Применяет цену на товар если она отличается.
 **/
$newPrices = '';
function tps_csv_apply_product_row($lineArr, $city) {

	//индексы колонок/полей в массиве
	$iIDKram = 0; $iIDSlav   = 1;   $iIDArt    = 2;  $iIDKonst = 3;
	$iName   = 4; $iPriceUahSet= 5; $iPriceUsd = 6;
	$iCateg  = 7; $iPriceUahCalc=8; $iCurrencyName = 9;  $iCurrencyRate = 10;
	$iPriceRewrite = 11; $iPriceFinal = 12;
	$iPriceKram=13;$iPriceSlav=14;  $iPriceArt = 15; $iPriceKonst=16;
	global $newPrices;

	//select NID of the product for given city
	switch ($city) {
		case 'kramatorsk':
			$nid = trim($lineArr[ $iIDKram ]);
			break;
		case 'slavyansk':
			$nid = trim($lineArr[ $iIDSlav ]);
			break;
		case 'artemovsk':
			$nid = trim($lineArr[ $iIDArt ]);
			break;
		case 'konstantinovka':
			$nid = trim($lineArr[ $iIDKonst ]);
			break;
		default:
			return 'city selection is wrong';
			break;
	}
	if(!is_numeric($nid)) return 'nid  not set ' . $nid;
	if($nid==0) return 'zero nid';

	$newPrice = $lineArr[ $iPriceFinal ];
	$newPrice = str_replace(',', '.', $newPrice);
	if(!is_numeric($newPrice)) return "new price for $nid can't be set, check it out: $newPrice";

	//попытаться использовать перезаписанные значения
	switch ($city) {
		case 'kramatorsk':
			$forcePrice = trim($lineArr[ $iPriceKram ]);
			break;
		case 'slavyansk':
			$forcePrice = trim($lineArr[ $iPriceSlav ]);
			break;
		case 'artemovsk':
			$forcePrice = trim($lineArr[ $iPriceArt ]);
			break;
		case 'konstantinovka':
			$forcePrice = trim($lineArr[ $iPriceKonst ]);
			break;
	}
	if($forcePrice) $forcePrice = str_replace(',', '.', $forcePrice);
	if(is_numeric($forcePrice)) $newPrice = $forcePrice;


	//запись в базу данных
	switch ($city) {
		case 'kramatorsk':
		case 'slavyansk':
			$field_prefix = '';
			break;
		case 'artemovsk':
		case 'konstantinovka':
			$field_prefix = 'field_';
			break;
	}

	//get old price
	$oldPrice = db_select("field_data_{$field_prefix}price","t")->fields("t",array("{$field_prefix}price_value"))->condition("entity_id", $nid)->range(0,1)->execute()->fetchfield();
	if(($newPrice==$oldPrice)) return "prices equal nid:$nid price=$newPrice<br>\r\n";


	$q = "UPDATE {field_data_{$field_prefix}price}  SET  `{$field_prefix}price_value` =  '$newPrice' WHERE `bundle`='product' AND `entity_id` =$nid";
	db_query($q);
	$q = "UPDATE {field_revision_{$field_prefix}price}  SET  `{$field_prefix}price_value` =  '$newPrice' WHERE `bundle`='product' AND `entity_id` =$nid";
	db_query($q);
	echo "$q<br>";
	$newPrices .= date('d.m.o').";id $nid;CTAPA9 $oldPrice; HOBA9 $newPrice\r\n";
}

//update price
//tps_csv_update_price('kramatorsk');

/*
//add to file
$newPrices .= file_get_contents('newprice.csv');
file_put_contents('newprice.csv', $newPrices);

//send mqtt message
require("phpMQTT.php");
$server = "m20.cloudmqtt.com";
$port   = 10992;
$user   = "yujcrqkx";
$pass   = "MI1jNJNBeyBU";
$client = "shopKram";

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client);
if($mqtt->connect(true, NULL, $user, $pass)) {
  $mqtt->publish("shop/1/price/updatevalues", time(), 0, 1);
} else {
  echo "time out mqtt";
}

//$mqc->connect();
*/