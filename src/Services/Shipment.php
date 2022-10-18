<?php

namespace Parcelshere\Services;

/**
 * @name SC0005 Shipment Service
 * @desc The shipment service provides functionality to make and manage shipments.
 * @author	Carolus Reinecke <carolus@parcelshere.co.uk>
 * @link	http://www.parcelshere.com
 * @copyright Parcelshere Ltd.
 */

class Shipment implements ServiceInterface {

    private $client;

    public function __construct($api_client)
    {
        $this->client = $api_client;
    }
    
    //SC0005-009 Get Exchange Options
    public function getExchangeOptions($shipmeent_detail)
    {
        return $this->client->request('POST', '/api/shipment/1/exchange/options', $shipmeent_detail);
    }

    //SC0005-011 Get Retail Profile
    public function getRetailProfile()
    {
        return $this->client->request('GET', '/api/shipment/4/profile', []);
    }

    //SC0005-012 Retrieve Exchange Methods
    public function getPackages()
    {
        return $this->client->request('GET', '/api/shipment/3/exchange/methods', []);
    }

    //SC0005-013 Retrieve Exchange Location
    public function retrieveExchangeLocation($exchange_option)
    {
        return $this->client->request('PUT', '/api/shipment/4/exchange/location', $exchange_option);
    }

    //SC0005-015 Schedule Delivery
    public function scheduleDelivery($delivery)
    {
        return $this->client->request('POST', '/api/shipment/4/delivery', $delivery);
    }

    //SC0005-016 Update Scheduled Delivery
    public function updateScheduledDelivery($delivery)
    {
        return $this->client->request('PUT', '/api/shipment/3/delivery', $delivery);
    }

}

?>
