<?php

/*

$Id$

*/

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
	
	public function __construct($database)
	{
		
		if(!isset($database)){
			$this->db = new AdaptorMysql();
		}else{
			$this->db = $database;
		}
		
		$this->dom = new DomDocument;
		$this->dom->preserveWhiteSpace = false;
		
		$inputSocket = fopen('php://input','rb');
		$contents = stream_get_contents($inputSocket);
		fclose($inputSocket);
		//GLOBALS['HTTP_RAW_POST_DATA'] - does not work without php.ini tweaking
		$this->dom->loadXML($contents);
		
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
	
	public function formatRecord($row,$options='')
	{
		$r = '';
		if(!isset($options['rows'])){
			foreach($row as $key=>$value){
				if(!is_numeric($key)){
					$r .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
				}
			}
		}else{
			foreach($options['rows'] as $key){
				$r .= '<' . $key . '><![CDATA[' . $row[$key] . ']]></' . $key . '>';
			}			
		}
		return $r;
	}
	
	public function formatRecordSet($recordSet,$options='')
	{
		$r = '<RecordSet Type="' . $options['name'] . '">';
		foreach($recordSet as $row){
			$r .= '<Row>';
			$r .= $this->formatRecord($row,$options);
			$r .= '</Row>';
		}
		$r .= '</RecordSet>';
		return $r;
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