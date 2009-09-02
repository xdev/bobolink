<?php

/*

Class: EmailProtector

Protects and safeguards email addresse
Requires use of a javascript decryption script
Requires Utils class
Requires either MCRYPT module OR des.php

*/

class EmailProtector
{
	
	private	$_em_key;
	private	$mode;
	
	public function __construct($m=true)
	{
		$this->_em_key = substr(sha1(microtime()),0,24);
		if($m === false){
			$this->mode = 'file';
		}else{
			$this->mode = 'native';
		}
	}
	
	public function getKey()
	{
		return $this->_em_key;
	}
	
	public function printKey()
	{
		print '
		<script type="text/javascript">
			// <![CDATA[
			var _em_key = "' . $this->_em_key . '";
			// ]]>
		</script>';
	}
	
	private function encodeEmail($matches)
	{				
		if($this->mode == 'native'){
			$iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$str = $matches[1].'@'.$matches[2];
			$e = mcrypt_encrypt(MCRYPT_TRIPLEDES,$this->_em_key,$str,MCRYPT_MODE_CBC,$iv);
			return 'href="#send_email" rev="em_' . strlen($str) . '_' . substr(Utils::stringToHex($e),2) . '_' . substr(Utils::stringToHex($iv),2) . '" class="em_encrypted_native" ';
		}
		if($this->mode == 'file'){
			$enc = substr(stringToHex(des($this->_em_key,$matches[1].'@'.$matches[2],1,0,null)),2);
			return 'href="#send_email" rev="em_' . $enc . '" class="em_encrypted_file" ';
		}
	}
	
	public function formatEmail($txt='',$format='\1 (at) \2')
	{
		if(function_exists('mcrypt_create')){
			$text = preg_replace_callback(
				'/href="mailto:([a-zA-Z0-9._-]+)@([a-zA-Z0-9._-]+)"/',
				array($this, 'encodeEmail'),
				$txt
			);
		
			$patterns = array(
						'/([a-zA-Z0-9._-]+)@([a-zA-Z0-9._-]+)/'
						);

			$replacements = array(
						$format
						);

			$text = preg_replace($patterns,$replacements,$text);
			return $text;
		}else{
			return $txt;
		}
	}
	
}

