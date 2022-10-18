<?php

namespace Parcelshere;

use Auth0\SDK\Auth0;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\JWTVerifier;

use GuzzleHttp\Client as ApiClient;
use Parcelshere\Common\Token;
use Lcobucci\JWT\Parser;
use Exception;

use Parcelshere\Services\Packaging;
use Parcelshere\Services\Tracking;
use Parcelshere\Services\Fallback;
use Parcelshere\Services\Shipment;

class RetailerClient {

    private $token;
    private $apiBase;
    private $clientDomain;
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $clientScope;
    private $audience;

    private $authClient = null;
    private $dataStore;
    private $saveDataFunction;
    private $loadDataFunction;


    public $packaging;    
    public $tracking;
    public $shipment;
    public $fallback;
    
    public function __construct()
    {
        $this->token = new Token();

        $this->packaging = new \Parcelshere\Services\Packaging($this);
        $this->tracking = new \Parcelshere\Services\Tracking($this);
        $this->shipment = new \Parcelshere\Services\Shipment($this);
        $this->fallback = new \Parcelshere\Services\Fallback($this);
        
    }

    public function getApiBase()
    {
        return $this->apiBase;
    }

    /**
     * Configures the instance
     * 
     */
    public function configure($config)
    {
        echo "Configuring....\n";
        $this->apiBase = $config['api_base'];
        $this->clientDomain = $config['client_domain'];
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUri = $config['redirect_uri'];
        $this->clientScope = $config['client_scope'];
        $this->audience = $config['audience'];
        $this->dataStore = $config['data_store'];
        $this->loadDataFunction = $config['load_function'];
        $this->saveDataFunction = $config['save_function'];

    }

    
    /**
     * Instantiates the authentication class
     * 
     */
    public function authenticate() 
    {
        $this->authClient = new Authentication(
            $this->clientDomain,
            $this->clientId,
            $this->clientSecret
        );
    }

    
    /**
     * Get Access Token:
     *
     */
    private function getAccessToken() {
        
        //echo "-> get token from db\n";
        if ((!$this->token->access_token) || (!$this->token->expires_at)) {
            try {

                $loadFunction = $this->loadDataFunction;
                $t_token = $loadFunction($this->dataStore, 'retailer_token');
                
                if ($t_token) {
                    $this->token = unserialize($t_token);
                } else {
                    $this->token = new Token();
                    //throw new \Exception("Could not find any record for user ".$this->username.". If this user exists then get the user's consent first and exchange the code for a token.", 401);
                    
                }
                
            } catch (Exception $e) {
//                die("Could not access the data store record.");
                throw new \Exception("Could not access the data store record.", 500);
                
            }
        }
        
        if ($this->token->hasExpired()) {

            try {
            
                $access_token_result = $this->authClient->client_credentials([
                     'audience' => $this->audience,
                ]);
            
            } catch (Exception $e) {
            
                die($e->getMessage());
                
            }
        
            // Get the tokens
            $_access_token = $access_token_result['access_token'];

            $_access_token = trim($_access_token);

            $_token = (new Parser())->parse((string) $_access_token);

            $this->token->set(
                (int)$_token->getClaim('http://pahe.co/uid'),
                $_access_token,
                null,
                (int)$_token->getClaim('exp')
            );

            $saveFunction = $this->saveDataFunction;
            $saveFunction($this->dataStore, "retailer_token", serialize($this->token));
            
        }
    }


    /**
     * 
     * Performs authenticated request.
     *
     * @param string  $method           - Method of request GET|POST|PUT|DELETE
     * @param string  $uri              - URI of the provided service.
     * @param array   $data             - Data to be sent in request.
     *
     * Example request('POST', '/api/account/1/users', ['x' => ['y' => 123, 'z' => 'abc']])
     *
     * @return array containing http status code and response.
     **/
    public function request($method, $api_path, $data = []) 
    {
        
        //Gets the retailers access token
        $this->getAccessToken();
        
        try {
            
            $apiClient = new ApiClient([
                'base_uri' => $this->apiBase,
            ]);

            $response = $apiClient->request($method, $api_path, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token->access_token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json;charset=UTF-8',
                ],
                'json' => $data
            ]);
            
            if (200 == $response->getStatusCode()) {
                $response = $response->getBody();
                $arr_result = json_decode($response, true);
                return $arr_result;
            }
        } catch (\Exception $e) {
            throw new Exception('error calling api - '. $e->getMessage());
        }
        return;
    }
}

?>

