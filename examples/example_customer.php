<?php
require_once '../vendor/autoload.php';

$customerDataStore = new \SleekDB\Store("customer_data", "DB/");

//------------------------------------------- Customer ---------------------------------------------------
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


//------------------------------------------- Customer ---------------------------------------------------

$customerClient = new Parcelshere\CustomerClient();

$customerClient->configure([
    'api_base' => 'https://staging-api.parcelshere.com',
    'client_domain' => 'auth.parcelshere.com',
    'client_id' => 'xxxx',
    'client_secret' => 'xxxx',
    'redirect_uri' => "https://xxxx/json/phr/1/cb/consent",
    'client_scope' => 'offline_access retail:profile:read retail:exchange:read retail:schedule:write retail:fallback:read',
    'audience' => 'https://api.parcelshere.com/v1',
    'data_store' => $customerDataStore,
    'load_function' => $loadData,
    'save_function' => $saveData
]);


$customerClient->authenticate();

$customerClient->setUsername("carolus");
echo "Username -> ".$customerClient->getUsername()."\n";
//$customerClient->getAccessToken();

echo "Uid -> ".$customerClient->getUserUid()."\n";

//    echo "--->Customer not found, present this consent link, to get permission,\n";
//    echo "--->".$customerClient->getConsentLink("bong")."\n";
//    exit();

//$code = 'yHCaZgXFz6nWDpYtaLUgDs4XrHh1j8HLtSvLPVSV91AhO';
//echo "--->".$customerClient->exchangeCode($code)."\n";
//exit();

//SC0005-001
$res = $customerClient->shipment->getRetailProfile();
$res_pretty = json_encode($res, JSON_PRETTY_PRINT);
echo "Get retail profile: ".$res_pretty."\n";
exit();

// $event = [
//     "as" => "sender",
//     "type" => "e0060",
//     "address_id" => 400,
//     "description" => "Ingredients Collected",
//     "ref" => "m1",
//     //"latitude" => -1.244555,
//     //"longitude" => -1.244555,
//     "address_city" => "Birmingham",
//     "address_country_code" => "GB"
// ];
//$tracking = $retailerClient->tracking->createEvent(1748, $event);
//$tracking_pretty = json_encode($tracking, JSON_PRETTY_PRINT);
//echo 'Create Event: '.$tracking_pretty.'\n';
