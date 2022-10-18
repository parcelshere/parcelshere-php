<?php
require_once '../vendor/autoload.php';


$retailerDataStore = new \SleekDB\Store("retailer_data", "DB/");

//------------------------------------------- Storage ---------------------------------------------------
$saveData = function($dataStore, $key, $value) {

    
    echo "Saving data.".$key."...\n";
    
    
    $settings = $dataStore->findBy(["key", "=", $key]);

    
    $setting = $settings[0];
    
    echo "found this settings ".$setting['_id']." :\n";
    //var_dump($setting);
    
    if ($settings) {
        
        $setting["value"] = $value;
        $dataStore->update($setting);

        echo "   ...updated\n";
        
    } else {
        $user = $dataStore->insert(
            [
                'key' => $key,
                'value' => $value
            ]);
        echo "   ...created\n";
    }
};

// This Function will retrieve the retailer token and return it.
$loadData = function($dataStore, $key) {
   
    $settings = $dataStore->findBy(["key", "=", $key]);
    return $settings[0]["value"];
   
};
//------------------------------------------- Retailer ---------------------------------------------------



$retailerClient = new Parcelshere\RetailerClient;

$retailerClient->configure([
    'api_base' => 'https://staging-api.parcelshere.com',
    'client_domain' => 'auth.parcelshere.com',
    'client_id' => '7u7AkuO2f9Ta993petOAQHtjrPTOTIbz',
    'client_secret' => 'cLhwCGL5IFL1lR1RAk2cd233556tQiZWJBWXRtpRV6ETv19IhygfgaAWh',
    'redirect_uri' => 'https://www.doson.co.uk/parcelshere/retailer',
    'client_scope' => 'openid offline_access user retailer',
    'audience' => 'https://api.parcelshere.com/v1',
    'data_store' => $retailerDataStore,
    'load_function' => $loadData,
    'save_function' => $saveData
]);

$retailerClient->authenticate();

//SC0044-002
$packages = $retailerClient->packaging->getPackages();
$packages_pretty = json_encode($packages, JSON_PRETTY_PRINT);
echo 'Packages found: '.$packages_pretty.'\n';


//SC0044-003
//$package = $retailerClient->packaging->getPackage('108cee4e46ac8261');
//$package_pretty = json_encode($package, JSON_PRETTY_PRINT);
//echo 'Package found: '.$package_pretty.'\n';

//SC0044-007 Create Packing Instructions
$items = [
	"items"=> [
		[
			"item_id"=> "12345",
			"description"=> "White Elephant",
			"width"=> 100,
			"length"=> 20,
			"height"=> 220,
			"weight"=> 100000,
			"is_liquid"=> false,
			"is_fragile"=> false,
			"is_perishable"=> false,
			"keep_flat"=> false,
			"qty"=> 5
		],
		[
			"item_id"=> "33333",
			"description"=> "White Elephant3",
			"width"=> 60,
			"length"=> 220,
			"height"=> 220,
			"weight"=> 200000,
			"is_liquid"=> false,
			"is_fragile"=> false,
			"is_perishable"=> false,
			"keep_flat"=> false,
			"qty"=> 8
		]
	]
];
//$instructions = $retailerClient->packaging->createPackingInstructions($items);
//$instructions_pretty = json_encode($instructions, JSON_PRETTY_PRINT);
//echo 'Packing instructions found: '.$instructions_pretty.'\n';

//SC0020-001
//$res = $retailerClient->tracking->getTypes(1748);
//$res_pretty = json_encode($res, JSON_PRETTY_PRINT);
//echo 'Event types: '.$res_pretty.'\n';

//SC0020-002 Create Event
$event = [
    "as" => "sender",
    "type" => "e0060",
    "address_id" => 400,
    "description" => "Ingredients Collected",
    "ref" => "m1",
    //"latitude" => -1.244555,
    //"longitude" => -1.244555,
    "address_city" => "Birmingham",
    "address_country_code" => "GB"
];
//$tracking = $retailerClient->tracking->createEvent(1748, $event);
//$tracking_pretty = json_encode($tracking, JSON_PRETTY_PRINT);
//echo 'Create Event.\n';

//SC0020-003
//$tracking = $retailerClient->tracking->retrieveEvents(1748, ['w'=>0]);
//$tracking_pretty = json_encode($tracking, JSON_PRETTY_PRINT);
//echo "Packing instructions found: ".$tracking_pretty."\n";

//echo "--->". $retailerClient->verify()."\n";


//SC0043-006 Get Safe Places
//$res = $retailerClient->fallback->getSafePlaces("person", "primary");
//$res_pretty = json_encode($res, JSON_PRETTY_PRINT);
echo 'Get Safe Places : '.$res_pretty.'\n';

// //SC0043-006 Get Safe Places
// $res = $retailerClient->fallback->getSafePlaces2("person", "primary");
// $res_pretty = json_encode($res, JSON_PRETTY_PRINT);
// echo 'Get Safe Places 2: '.$res_pretty.'\n';

