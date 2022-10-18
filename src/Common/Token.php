<?php
/**
 * 
 * The Token class encptulates the three token attributes. It is 
 * used as part of the Parcelshere API client to access the Parcelshere
 * API to enable Retailer intetgration.
 * 
 * @author Carolus Reinecke
 *
 */

namespace Parcelshere\Common;

class Token
{

    public $user_uid;
    public $access_token;
    public $refresh_token;
    public $expires_at;
    
    public function __construct()
    {
        $user_uid = 0;
        $access_token = null;
        $refresh_token = null;
        $expires_at = 0;
    }

    /**
     * 
     * Sets the token values
     * 
     * @param unknown $access_token
     * @param unknown $refresh_token
     * @param unknown $expires_at
     */
    public function set($user_uid, $access_token, $refresh_token, $expires_at)
    {
        $this->user_uid = (int)$user_uid;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->expires_at= (int)$expires_at;
    }

    
    /**
     *
     * Is token set
     *
     */
    public function isSet() {
        if (($this->user_uid > 0) || ($this->expires_at > 0)) {
            return true;
        }
        return false;
    }
    
    
    /**
     * 
     * Check to see if the token has expired
     * 
     */
    public function hasExpired() {
        
        $now = time();
        if ((int)$this->expires_at < ((int)$now + 10)) {
            return true;
        }
        
        return false;
    }
}
