<?php

namespace Parcelshere\Services;

/**
 * @name      SC0043 Fallback Service
 * @desc      The fallback service provides functionality to allow the users to effectively manage fallback location preferences and rules.
 * @author	  Carolus Reinecke <carolus@parcelshere.co.uk>
 * @link	  http://www.parcelshere.com
 * @copyright Parcelshere Ltd.
 */

class Fallback implements ServiceInterface {

    private $client;

    public function __construct($api_client)
    {
        $this->client = $api_client;
    }
    
    //SC0043-001 Upsert Preferences
    public function upsertPreferences($preferences)
    {
        return $this->client->request('PUT', $this->client->base_uri, '/api/fallback/1/preferences', $this->client->token, $preferences);
    }
        
    //SC0043-002 Get Preferences
    public function getPreferences()
    {
        return $this->client->request('GET', '/api/fallback/1/preferences', []);
    }

    //SC0043-003 Get Rules
    public function getRules($moe, $qualifier)
    {
        return $this->client->request('GET', '/api/fallback/1/rules/'.$moe.'/person/qualifier/'.$qualifier, []);
    }

    //SC0043-004 Set Rules
    public function setRules($moe, $qualifier)
    {
        return $this->client->request('PUT', '/api/fallback/1/rules/'.$moe.'/person/qualifier/'.$qualifier, []);
    }

    //SC0043-005 Get Full Address
    public function getFullAddress()
    {
        return $this->client->request('GET', '/api/fallback/1/addresses/person/primary', []);
    }

    //SC0043-006 Get Safe Places
    public function getSafePlaces($moe, $qualifier)
    {
        return $this->client->request('GET', '/api/fallback/1/safeplaces/'.$moe.'/qualifier/'.$qualifier, []);
    }
}

?>
