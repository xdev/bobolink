<?php

header('Content-Type: text/xml');

Class FlashMW
{
	
	public $db;
	public $dom;
	public $action;
	public $response;
	public $errors;
	
	public $params;
	public $payload;
	
	public function __construct()
	{
	
		$this->db = new AdaptorMysql();
		
		$this->dom = new DomDocument;
		$this->dom->preserveWhiteSpace = false;
		$this->dom->loadXML($GLOBALS['HTTP_RAW_POST_DATA']);
		
		$this->response = array();
		$this->errors = array();
		
		//action
		$this->action = $this->dom->firstChild->getAttribute('Type');
		
		//payload
		if($this->payload = $this->dom->getElementsByTagName('Payload')->item(0)){
			
		}else{
			$this->payload = null;
		}
				
		//params			
		if($params = $this->dom->getElementsByTagName('Param')){
			
			$this->params = array();
			foreach($params as $param)
			{
				$t = $param->getAttribute('Name');
				if($param->hasChildNodes()){
					$this->params[$t] = $param->firstChild->nodeValue;
				}else{
					$this->params[$t] = '';
				}	
			}
		
		}
		
		
	
	}
		
	public function sendResponse()
	{
		
		$xml = '<Response Type="' . $this->action . '">';
		
		if(isset($this->response['payload'])){
			$xml .= '<Payload>' . $this->response['payload'] . '</Payload>';
		}
		
		$xml .= '<Message>';
		
		if(count($this->errors) == 0){
			$xml .= '<Success />';
		}else{
			
			$xml .= '<Errors>';
			foreach($this->errors as $error){
				$xml .= '<Error>' . $error . '</Error>';			
			}
			$xml .= '</Errors>';
			
		}
		
		$xml .= '</Message>';
		
		$xml .= '</Response>';
		
		echo $xml;
		
	}
	
}
	
?>