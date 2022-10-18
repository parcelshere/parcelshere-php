<?php

namespace Parcelshere\Services;

use Parcelshere\Common\Utils;

/**
 * @name SC0020 Shipment Tracking Service
 * @desc The shipment tracking service provides functionality to allow the users to track shipments.
 * @author	Carolus Reinecke <carolus@parcelshere.co.uk>
 * @link	http://www.parcelshere.com
 * @copyright Parcelshere Ltd.
 */

class Tracking implements ServiceInterface {

    private $client;

    public function __construct($api_client)
    {
        $this->client = $api_client;
    }

    //SC0020-001 Get Event Types (v2)
    public function getTypes()
    {
        return $this->client->request('GET', '/api/tracking/2/types', null);
    }

    //SC0020-002 Create Event
    public function createEvent($shipment_no, $event)
    {
        return $this->client->request('POST', '/api/tracking/1/shipments/'.$shipment_no.'/events', $event);
    }

    //SC0020-003 Retrieve Events
    public function retrieveEvents($shipment_no, $params = [])
    {
        $param_str = Utils::queryString($params);
        return $this->client->request('GET', '/api/tracking/1/shipments/'.$shipment_no.'/events'.$param_str, null);
    }

}
?>
