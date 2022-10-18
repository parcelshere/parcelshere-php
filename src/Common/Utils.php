<?php

namespace Parcelshere\Common;

use GuzzleHttp\Client;

class Utils {

   public static function queryString($params) {
       $f = true;
       foreach ($params as $key => $val){
           if ($f) {
               $f = false;
               $s = "?";
           } else {
               $s = "&";
           }
           $part .= $s.$key."=".urlencode("".$val);
       }
       return $part;
    }
    
}
