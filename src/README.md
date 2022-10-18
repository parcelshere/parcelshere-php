# Parcelshere API v2 PHP SDK

It is an official PHP SDK for Parcelshere API v1.

You can find more examples and information about Parcelshere API v2 here: [https://help.parcelshere.com/developers/](https://help.parcelshere.com/developers/)

## Getting started

In order to use this library you need to have at least PHP 7.??? version.

There are two ways to use Parcelshere PHP SDK:

##### Use [Composer](https://getcomposer.org/)

If you are not familiar with Composer, learn about it [here](https://getcomposer.org/doc/01-basic-usage.md).

Then you will need to run this simple command using CLI:

```
composer require parcelshere/parcelshere-php
```

This library is built atop of

##### Manual (preferable for shared hostings)

This way is preferable only if you are using shared hosting and do not have a possibility to use Composer. You will need to download [this archive](https://bit.ly/32jmi7M)(v0.2.3), extract it and place its contents in root folder of your project. The next step is the same as using Composer, you will need to require `vendor/autoload.php` file in your index.php and lets dive in!

## Usage examples

#### Retailer Client API

In the given example you will see how to initiate selected API and a few actions which are available:

- Retrieve Tracking Events


```php


$retailerClient = new Parcelshere\RetailerClient;  // instantiates the retail client

$retailerClient->configure([  // create a configuration
    'api_base' => 'https://staging-api.parcelshere.com',
    'client_domain' => 'auth.parcelshere.com',
    'client_id' => 'xxxx',
    'client_secret' => 'xxxx',
    'redirect_uri' => 'https://xxxx/redirect/uri',
    'client_scope' => 'openid offline_access user',
    'audience' => 'https://api.parcelshere.com/v1',
    'data_store' => $retailerDataStore,
    'load_function' => $loadData,
    'save_function' => $saveData
]);

$retailerClient->authenticate();  // authenticates

//SC0020-003 Retrieve Tracking Events
$tracking = $retailerClient->tracking->retrieveEvents(1748, ['w'=>0]);

$tracking_pretty = json_encode($tracking, JSON_PRETTY_PRINT);

echo "Packing instructions found: ".$tracking_pretty."\n";

```

#### Customer Client API

In the given example you will see how to initiate selected API and a few actions which are available:

- Retrieve Tracking Events


```php


$customerClient = new Parcelshere\CustomerClient();

$customerClient->configure([
    'api_base' => 'https://staging-api.parcelshere.com',
    'client_domain' => 'auth.parcelshere.com',
    'client_id' => 'xxxx',
    'client_secret' => 'xxxx',
    'redirect_uri' => 'https://xxxx/redirect/uri',
    'consent_redirect_uri' => "https://parcelshere.test/json/phr/1/cb/consent", ???
    'client_scope' => 'openid offline_access retail:profile:read retail:exchange:read retail:schedule:write retail:fallback:read',
    'audience' => 'https://retailer.api.parcelshere.com/v1',
    'data_store' => $customerDataStore,
    'load_function' => $loadData,
    'save_function' => $saveData
]);


$customerClient->authenticate();

$customerClient->setUsername("marygogo");  //setting the username.... we might move this into the constructor but will need the config set first??


//SC0005-001
$res = $customerClient->shipment->getRetailProfile();
$res_pretty = json_encode($res, JSON_PRETTY_PRINT);
echo "Get retail profile: ".$res_pretty."\n";

```

## Support and Feedback

In case you find any bugs, submit an issue directly here in Git???.

You are welcome to create SDK for any other programming language.

If you have any issues using our API or SDK free to contact our support by email [info@Parcelshere.com](mailto:info@Parcelshere)

Official documentation is at [https://help.parcelshere.com/developers/](https://help.parcelshere.com/developers/)