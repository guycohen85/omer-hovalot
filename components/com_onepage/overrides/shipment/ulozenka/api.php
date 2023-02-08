<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

require_once(dirname(__FILE__).DS.'rest.php'); 

class ulozenkaApi extends RestRequest {
  private $shopId; 
  private $key; 
  private $partner; 
  private $inactive; 
  private $apiurl; 
  private $rest; 
  private $defaults; 
  public $error; 
  function __construct($params)
   {
      $this->shopId = (int)$params->shopid; 
	  $this->key = $params->key; 
	  $this->partner = (int)$params->partners; 
	  if (!isset($params->inactive))
	  $this->inactive = 0; 
	  else 
	  $this->inactive = $params->inactive; 
	  
	  $apiurl = $params->api_url; 
	 
	  $apiurl = str_replace('http://', '', $apiurl); 
	  $apiurl = str_replace('https://', '', $apiurl); 
	  $apiurl = str_replace('/v2/', '', $apiurl); 
	  
	  
	  
	  if (empty($apiurl)) $apiurl = 'api.ulozenka.cz'; 
	  
	  $this->defaults = array(); 
	  $this->defaults['X-Shop'] = $params->shopid;
	  $this->defaults['X-Key'] = $params->key; 
	  $this->extraHeader = $this->defaults; 
	  
	  $this->apiurl = 'https://'.$apiurl; 
	  
	  
      $this->rest = new RestRequest($this->apiurl, 'GET', $this->defaults); 
   }
   
   function getPobocky()
    {
	   //https://private-834db-ulozenka.apiary-proxy.com/v2/branches?5994,0,1
	   
	   
	   $this->rest->url .= '/v2/branches?'.(int)$this->shopId.','.(int)$this->inactive.','.(int)$this->partner; 
	   $this->rest->acceptType = 'application/xml'; 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   
	   if (!empty($this->rest->error)) $this->error = $this->rest->error; 
	   if (empty($body)) $body = ''; 
	   
	   
	   return $body; 
	   
	   
	}
}