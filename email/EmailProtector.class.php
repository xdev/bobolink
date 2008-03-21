<?php

/*

$Id: $

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
	
	public function __construct()
	{
		$this->_em_key = substr(sha1(microtime()),0,24);
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
		$iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$str = $matches[1].'@'.$matches[2];
		$e = mcrypt_encrypt(MCRYPT_TRIPLEDES,$this->_em_key,$str,MCRYPT_MODE_CBC,$iv);
		return 'href="#send_email" rev="em_' . strlen($str) . '_' . substr(Utils::stringToHex($e),2) . '_' . substr(Utils::stringToHex($iv),2) . '" class="em_encrypted" ';
	}
	
	public function formatEmail($txt='')
	{
		
		$text = preg_replace_callback(
			'/href="mailto:([a-zA-Z0-9._-]+)@([a-zA-Z0-9._-]+)"/',
			array($this, 'encodeEmail'),
			$txt
		);
		
		$patterns = array(
					'/([a-zA-Z0-9._-]+)@([a-zA-Z0-9._-]+)/'
					);

		$replacements = array(
					'\1'
					);

		$text = preg_replace($patterns,$replacements,$text);
		return $text;		
	}
	
}

