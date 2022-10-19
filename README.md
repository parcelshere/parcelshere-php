# Parcelshere PHP bindings

The Parcelshere PHP library provides convenient access to the Parcelshere API from
applications written in the PHP language. It includes a pre-defined set of
classes for API resources that initialize themselves dynamically from API
responses which makes it compatible with a wide range of versions of the Parcelshere
API.



## Getting started

In order to use this library you need to have at least PHP 7.? version.

There are two ways to use Parcelshere PHP SDK:

##### Use [Composer](https://getcomposer.org/)

If you are not familiar with Composer, learn about it [here](https://getcomposer.org/doc/01-basic-usage.md).

Then you will need to run this simple command using CLI:

```
composer require parcelshere/parcelshere-php
```

##### Manual (preferable for shared hosting)

This way is preferable only if you are using shared hosting and do not have a possibility to use Composer. You will need to download [this archive](??)(v0.2.3), extract it and place its contents in root folder of your project. The next step is the same as using Composer, you will need to require `vendor/autoload.php` file in your index.php and lets dive in!

## Usage examples

#### Token Store
First load the simple db libraries.

```
composer require rakibtg/sleekdb

```

This is an example that can be used to store and retrieve tokens in a key-value store.


```php

$saveData = function($dataStore, $key, $value) {  // function to store key values

    $settings = $dataStore->findBy(["key", "=", $key]);

    if ($settings) {
        $settings["value"] = $value;
        $dataStore->updateById((int)$settings->_id, $settings);
    } else {
        $user = $dataStore->insert(
            [
                'key' => $key,
                'value' => $value
            ]);
    }
};

$loadData = function($dataStore, $key) {  // function to retrieve values of a stored key

    $settings = $dataStore->findBy(["key", "=", $key]);
    return $settings[0]["value"];

};

```

This can be used for both examples below.


#### Retailer

In the given example the retailer is making a call to retrieve tracking events for a given shipment (shipment: 1748):

- Retrieve Tracking Events

```php

$retailerDataStore = new \SleekDB\Store("retailer_data", "DB/");

//see $loadData and $saveData example above

$retailerClient = new Parcelshere\RetailerClient;

$retailerClient->configure([
    'api_base' => 'https://staging-api.parcelshere.com',
    'client_domain' => 'auth.parcelshere.com',
    'client_id' => 'xxxx',
    'client_secret' => 'xxxx',
    'redirect_uri' => 'https://xxxx/redirect/uri',
    'client_scope' => 'openid offline_access user retailer',
    'audience' => 'https://api.parcelshere.com/v1',
    'data_store' => $retailerDataStore,
    'load_function' => $loadData,
    'save_function' => $saveData
]);

$retailerClient->authenticate();

//SC0020-003 Retrieve Tracking Events
$shipment_no = 1748;
$tracking = $retailerClient->tracking->retrieveEvents($shipment_no, ['w'=>0]);  //Retrieve event s for shipment 1748
$tracking_pretty = json_encode($tracking, JSON_PRETTY_PRINT);
echo "Shipment ".$shipment_no.": ".$tracking_pretty."\n";

```

#### Customer

In the given example the retailer is making a call on behalf of the customer (Parcelshere user "marygogo") to retrieve the user's "retailer" or shopping profile:

- Get Retail Profile


```php


$customerDataStore = new \SleekDB\Store("customer_data", "DB/");

//see $loadData and $saveData example above

$customerClient = new Parcelshere\CustomerClient();

$customerClient->configure([
    'api_base' => 'https://staging-api.parcelshere.com',
    'client_domain' => 'auth.parcelshere.com',
    'client_id' => 'xxxx',
    'client_secret' => 'xxxx',
    'redirect_uri' => 'https://xxxx/parcelshere/retailer/callback',
    'consent_redirect_uri' => "https://xxxx/json/phr/1/cb/consent",
    'client_scope' => 'openid offline_access retail:profile:read retail:exchange:read retail:schedule:write retail:fallback:read',
    'audience' => 'https://retailer.api.parcelshere.com/v1',
    'data_store' => $customerDataStore,
    'load_function' => $loadData,
    'save_function' => $saveData
]);

$customerClient->authenticate();

$customerClient->setUsername("marygogo");

//SC0005-011 Get Retailer Profile (Shopper)
$res = $customerClient->shipment->getRetailProfile();
$res_pretty = json_encode($res, JSON_PRETTY_PRINT);
echo "The retail profile for user "marygogo" is : ".$res_pretty."\n";

```


## Support and Feedback

In case you find any bugs, submit an issue directly here in GitHub.

You are welcome to create SDK for any other programming language.

If you have any troubles using our API or SDK free to contact our support by email [info@parcelshere.com](mailto:info@parcelshere)

Official documentation is at [https://help.parcelshere.com/docs/](https://help.parcelshere.com/docs)
