<?php

namespace Parcelshere;

use Auth0\SDK\Auth0;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\JWTVerifier;
use GuzzleHttp\Client as ApiClient;
use Parcelshere\Common\Token;
use Lcobucci\JWT\Parser;
use Exception;

use Parcelshere\Services\Fallback;
use Parcelshere\Services\Shipment;

class CustomerClient {

    private $username;
    //public $token;
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

    public $fallback;    // using the fallback service
    public $shipment;    // using the shipment service

    
    public function __construct()
    {
        $this->token = new Token();
        $this->fallback = new \Parcelshere\Services\Fallback($this);
        $this->shipment = new \Parcelshere\Services\Shipment($this);
    }


    /**
     * Configures the instance
     *
     */
    public function configure($config)
    {

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
     * Instantiate the client.
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
     * Sets the user name and loads the token
     * 
     */
    public function setUsername($username)
    {
        if (($this->username != $username) && ($username != null) && ($username != "")) {
            //We need to clear and reload token.
            $this->username = $username;
            $this->token = new Token();
        }
    }
        
    /**
     * Sets the user name and loads the token
     *
     */
    public function getUsername()
    {
        
        return $this->username;
        
    }
    
    
    /**
     * Returns the user uid.
     */
    public function getUserUid()
    {
        
        return (int)$this->token->user_uid;
        
    }
    

    /**
     * This provides a consent link which the user can click on to grant the
     * retailer consent to access their details. We do not know nothing about the user.
     * This will authenticate the user and return a code that we will need to
     * exchamge for an access_token
     */
    public function getConsentLink($state) {
        
        $url = 'https://' . $this->clientDomain . '/authorize';
        $url = $url . '?client_id=' . $this->clientId;
        $url = $url . '&response_type=code';
        $url = $url . '&scope=' . str_replace(' ', '%20', $this->clientScope);
        $url = $url . '&redirect_uri=' . $this->redirectUri;
        $url = $url . '&audience=' . $this->audience;
        $url = $url . '&nonce=nonce123';
        $url = $url . '&state=' . $state;
        
        // We will need to manage the state properly and provide
        // a random element in the as well as HASH or encode or
        // store the original intention before redirecting the request
        // possibly using a token and then lookup the intended state on
        // the return.
        
        return $url;
    }
    
    
    /**
     * Exchanges auth code for a access_token
     *
     * 1. Exchanges an authorisation code for an access token
     * 2. Validates the token
     * 3. Updates the class
     * 4. Stores the tokens under the user meta
     */
    public function exchangeCode($auth_code, $silent = false) {
        
        echo "Begin code exchange..\n";
                
        if (empty($auth_code)) {
            throw new Exception('auth_code is mandatory');
        }
        
        try {
            // Attempt to get an access_token with the code returned and original redirect URI.
            $code_exchange_result = $this->authClient->code_exchange($auth_code, $this->redirectUri);
        } catch (Exception $e) {
            // This could be an Exception from the SDK or the HTTP client.
            die('Code Exchange error ----> '. $e->getMessage());
        }
        
        echo "Res: ".$code_exchange_result.".\n"; 
        
        print_r($code_exchange_result, true);
                
        //$req_ce = print_r($code_exchange_result, true);
        $_access_token = trim($code_exchange_result['access_token']);
        $_token = (new Parser())->parse((string) $_access_token);
        
        $_username = $_token->getClaim('http://pahe.co/username');
        
        $this->token->set(
            (int)$_token->getClaim('http://pahe.co/uid'),
            $code_exchange_result['access_token'],
            $code_exchange_result['refresh_token'],
            (int)$_token->getClaim('exp')
        );
        $this->username = $_username;
        
        //Store the new token details for user UID
        $saveFunction = $this->saveDataFunction;
        $saveFunction($this->dataStore, $_username, serialize($this->token));

        echo "End code exchanged for user: ".$_username.".\n";
        
        return $_username;
    }
    
    
    /**
     * Has Access Token:
     *
     */
    public function hasToken() {
        
        $this->token->isSet();
        
    }
        
    
    /**
     * Get Access Token:
     *
     */
    public function getAccessToken() {
        //
        //loads access token from db
        //if expired refresh
        
        //echo "-> get token from db\n";
        if ((!$this->token->access_token) || (!$this->token->expires_at)) {
            try {
                
                $loadFunction = $this->loadDataFunction;
                $t_token = $loadFunction($this->dataStore, $this->username);
                
                if ($t_token) {
                    $this->token = unserialize($t_token);
                } else {
                    $this->token = new Token();
                    //throw new \Exception("Could not find any record for user ".$this->username.". If this user exists then get the user's consent first and exchange the code for a token.", 401);
                }
                
            } catch (Exception $e) {
                //die("Could not access the data store record.");
                throw new \Exception("Could not access the data store record.", 500);
                
            }
        }
        
        if ($this->token->hasExpired()) {
            
            $options['scope'] = $this->clientScope;
            echo "Begin refresh token..".$this->token->refresh_token." - scope: ".$options['scope']."\n";
            
            try {
                
                $refresh_token_result = $this->authClient->refresh_token($this->token->refresh_token, $options);
                echo "xxxx..".$refresh_token_result."\n";
            } catch (Exception $e) {
                echo('Refresh error ----> '. $e->getMessage());
                die($e->getMessage());
            }
            
            $req_ce = print_r($refresh_token_result, true);
            echo('Refresh ----> '. $req_ce);
            
            // Get the tokens
            $_access_token = trim($refresh_token_result['access_token']);
            
            $_token = (new Parser())->parse((string) $_access_token);

            $_refresh_token = trim($refresh_token_result['refresh_token']);
            
            
            print_r("-------->".$_token->getClaim('http://pahe.co/uid')."\n");
            $_username = $_token->getClaim('http://pahe.co/username');
            
            $this->token->set(
                (int)$_token->getClaim('http://pahe.co/uid'),
                $_access_token,
                $_refresh_token,
                (int)$_token->getClaim('exp')
            );
            
            if (($this->username == $_username) && ($this->username != null) && ($this->username != "")) {
                
                $saveFunction = $this->saveDataFunction;
                $saveFunction($this->dataStore, $this->username, serialize($this->token));

            } else {
                
                throw  new \Exception("Refresh token username does not match current token username.", 500);
                
            }
            
            echo "End refresh for user: ".$_username.".\n";
            
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
        
        //Gets the customer's access token
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

