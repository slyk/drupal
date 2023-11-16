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

if(@$_GET['tkn']!='tPsSt4F') {header("HTTP/1.1 401 Unauthorized"); die();}


//расширение заходит без куки, поэтому оно не залогинено
//а функция загрузки транзакций не работает без логина, она
//смотрит на uid, поэтому поставим uid кладовщика
global $user;
if($user->uid<=0) {$user->uid=21;}
//if($user->uid<=0) {header("HTTP/1.1 401 Unauthorized"); die();}

if(!isset($_GET['ttn'])) $ret = listTransactions( @$_GET['timestamp'] );
else $ret = @applyTTN($_GET['id'], $_GET['ttn']);

//test
/*$ret[0]->changed = time();
$ret[0]->note = '!! '.$ret[0]->note;
$ret = array($ret[0]);//*/

//RETURN AND EXIT:
header('Access-Control-Allow-Origin: *');
echo json_encode($ret);
die();

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////


function listTransactions($dateFrom) {
	module_load_include('php','tps_ent','TPSEntityFieldQuery');
	module_load_include('php','tps_ent','TransactionsRangeFilterVO');
	$filter = new TransactionsRangeFilterVO();
	$filter->status = array(10,20); //10 ordered; 20 payed
	$filter->type 	= array(1);
	$filter->limit  = 10;
	$filter->dateFrom= $dateFrom;
	$transactions = TPSTransAPI::range($filter);

	$ret = array();
	foreach ($transactions as $trans) {
		$tret = new StdClass();
		$tret->id = $trans->id;
		$tret->status = $trans->status;
		$tret->note = $trans->note;
		$tret->value = $trans->value;
		$tret->clID = $trans->usr_they;
		$tret->changed = $trans->changed;
		$client = user_load($tret->clID);
		$tret->clName =		$client->name;
		$tret->clFName=		@$client->fname['und'][0]['safe_value'];
		$tret->clSName=		@$client->surname['und'][0]['safe_value'];
		$tret->clPhone= 	@$client->phone['und'][0]['safe_value'];
		$tret->clAddress=	@$client->address['und'][0]['safe_value'];
		$tret->clCityTid=	@$client->city['und'][0]['tid'];
		if($tret->clCityTid) $cityTerm = taxonomy_term_load($tret->clCityTid);
		if($cityTerm) $tret->clCity = $cityTerm->name;

		$ret[] = $tret;
	}
	return $ret;
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////

function applyTTN($transID, $ttnNumber) {
	$transIDSymbolic = $transID;

	//check everything is ok and prepare data
	$haveMatch = preg_match('/^[0-9QWERTYUPASDFGHJKLZXCVBNM\-\+]+$/', $transID);
	if(!$haveMatch) return "error";					//math only allowed chars in transaction ID
	$transID = TPSTransAPI::IDstrTOint($transID);	//convert to numeric (key of transactions table)
	if(!is_numeric($transID)) return "error 2";		//check its ok and we have numeric id of transaction
	if(!is_numeric($ttnNumber)) return "error 3";	//ttn must be only numberic too
	if(strlen($ttnNumber)>18) return "error 5";
	if(strlen($transID)>8) return "error 6";

	//make query
	$query = "UPDATE {tps_transaction} SET `note` = CONCAT(`note`, ' TTN:$ttnNumber') WHERE `id` =$transID";
	db_query($query);

 	return array(msg=>"ok", transID=>$transIDSymbolic, ttn=>$ttnNumber);
}