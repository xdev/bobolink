<?php

/*

Class: Session

Collection of functions for general session handling.
This class is basic and needs some TLC to be more useful. Typically it's extended for specific use on a project.

*/

class Session
{

	private $_data;
	
	/*
	
	Constructor: __construct
	
	does nothing
	
	*/
	
	function __construct()
	{
		//$this->check();
	}
	
	/*
	
	Function: deleteVar
	
	deletes a variable from session with unset
	
	Parameters:
	
		name:String - name of variable to delete
	
	*/
	
	public function deleteVar($name)
	{
		unset($_SESSION[$name]);
	}
	
	/*
	
	Function: setVar
	
	sets a session var
	
	Parameters:
	
		name:String - name 
		value:String - value
	
	*/
	
	public function setVar($name,$value)
	{
		//session_name("s_id");
		//session_start();
		$_SESSION[$name] = $value;
		//print "<p class=\"error\">SESSION SET - $name=>$value</p>";
	}
	
	/*
	
	Function: getVar
	
	returns a value
	
	Parameters:
	
		name:String - name of variable to get
	
	Returns:
	
		value
	
	*/
	
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
	
	/*
	
	Function: __set
	
	saves properties into internal stash
	
	Parameters:
	
		name:String - var name
		value:Object - var value
	
	*/
	
	public function __set($name,$value)
	{
		$this->_data[$name] = $value;
	}
	
	/*
	
	Function: __get
	
	gets a property from the interval stash
	
	Parameters:
	
		name:String - var name
	
	Returns:
	
		value
		
	*/
	
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