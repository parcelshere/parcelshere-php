<?php

namespace Parcelshere\Services;

use Parcelshere\Common\Utils;

/**
 * @name SC0044 Packaging Service
 * @desc The pakaging service provides functionality to allow retailer to manage packaging matarial.
 * @author	Carolus Reinecke <carolus@parcelshere.co.uk>
 * @link	http://www.parcelshere.com
 * @copyright Parcelshere Ltd.
 */

class Packaging implements ServiceInterface {

    private $client;

    public function __construct($api_client)
    {
        $this->client = $api_client;
    }
    
    //SC0044-001 Add Package
    public function addPackage($packaging)
    {
        return $this->client->request('POST', '/api/packaging/1/packages', $packaging);
    }

    //SC0044-002 Get Packages
    public function getPackages($params = [])
    {
        $param_str = Utils::queryString($params);
        return $this->client->request('GET', '/api/packaging/1/packages'.$param_str, []);
    }

    //SC0044-003 Get Package
    public function getPackage($package_id)
    {
        return $this->client->request('GET', '/api/packaging/1/packages/'.$package_id, []);
    }

    //SC0044-004 Update Package
    public function updatePackage($packaging)
    {
        return $this->client->request('PUT', '/api/packaging/1/packages/'.$package_id, $packaging);
    }

    //SC0044-005 Delete Package
    public function deletePackage($package_id)
    {
        return $this->client->request('DELETE', '/api/packaging/1/packages/'.$package_id);
    }

    //SC0044-006 Update Package Quantity
    public function updatePackageQuantity($qty, $params = [])
    {
        $param_str = Utils::queryString($params);
        return $this->client->request('GET', '/api/packaging/1/packages/'.$package_id.'/qty'.$param_str, []);
    }

    //SC0044-007 Create Packing Instructions
    public function createPackingInstructions($items)
    {
        return $this->client->request('POST', '/api/packaging/1/packing/instructions',$items);
    }

}

?>
