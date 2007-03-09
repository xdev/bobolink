<?php
class Session
{

	private $_data;

	function __construct()
	{
		//$this->check();
	}
	/**
	* login
	* starts session and sets cookie
	*
	* @param   string   id
	* @param   string   password . jointime md5
	* @param   string   email
	* @param   number   duration of cookie
	*     
	*
	*/
	public function deleteVar($name)
	{
		unset($_SESSION[$name]);
	}
	
	public function setVar($name,$value)
	{
		//session_name("s_id");
		//session_start();
		$_SESSION[$name] = $value;
		//print "<p class=\"error\">SESSION SET - $name=>$value</p>";
	}
	
	public function getVar($name)
	{
		//session_name("s_id");
		//session_start();
		//print "<p class=\"error\">SESSION GET - $name=>$_SESSION[$name]</p>";
		if(isset($_SESSION[$name])){
			return $_SESSION[$name];
		}else{
			return false;
		}
		
	}
	
	
	public function __set($name,$value)
	{
		$this->_data[$name] = $value;
	}
	
	public function __get($name)
	{
		
		if(isset($this->_data[$name])){
			return $this->_data[$name];
		}else{
			return false;
		}
		
	}
	
	

}
?>