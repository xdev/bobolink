<?php

class Db
{

	/**
	* connect
	* connect to db using info from config.inc
	*
	*/
	private $connection;
	
	function __construct()
	{
		//print "CONSTRUCT DB";
		$this->openConnection();
	}
	
	function __destruct()
	{
		$this->closeConnection();
	}
	
	function openConnection()
	{
		$DB = $GLOBALS['DATABASE'];
				
		$this->connection = mysql_connect($DB['host'], $DB['user'], $DB['pass']);
		
		if (!$this->connection){
			die('Could not connect to the database: ' . mysql_error());
		}
		mysql_select_db($DB['db']);
	}
	
	function closeConnection()
	{
		mysql_close($this->connection);
	}
		
		
	//*/
	
	
	/**
	* querySimple
	* query and return a resource identifier
	*
	* @param   string   sql query
	*     
	* @return  array    mysql resouce link
	*/
	
	function sql($sql)
	{
		$r = mysql_query($sql) or die(mysql_error() . $sql);
		return $r;
	}
	
	/**
	* queryRow
	* query and return a row in array form
	*
	* @param   string   sql query
	*     
	* @return  array    associate/numeric
	*/
	
	public static function queryRow($sql)
	{
	
		$r = mysql_query($sql) or die(mysql_error() . $sql);
		$tA = mysql_fetch_array($r,MYSQL_BOTH);
		
		
		if(mysql_num_rows($r) == 0){
			return false;
		}else{
			mysql_free_result($r);
			return $tA;
		}
	
	}
	
	/**
	* query
	* query and return a record set in array form
	*
	* @param   string   sql query
	*     
	* @return  array    associate/numeric array
	*/
	
	public static function query($sql){
	
		$r = mysql_query($sql) or die(mysql_error() . $sql);
		if(mysql_num_rows($r) == 0){
			return false;		
		}else{		
			
			$tA = array();
			for($i=0;$i<mysql_num_rows($r);$i++){
				$tA[] = mysql_fetch_array($r,MYSQL_BOTH);
			}
			mysql_free_result($r);
			return $tA;			
			
		}
		
	}
	
	/**
	* insert
	* insert template
	*
	* @param   string   table
	* @param   string   cols (csv)
	* @param   array    values
	*     
	*/
	
	function insert($table,$row_data)
	{
		
		//need to make exception for mysql functions
		
		$sql = "INSERT INTO `$table` ";
		
		$i_cols = "";
		$i_vals = "";
			
			
		for($i=0;$i<count($row_data);$i++){
			$i_cols .= "`".$row_data[$i]['field']."`";
			$i_vals .= "'".  mysql_real_escape_string(stripslashes($row_data[$i]['value'])) . "'";
			if($i != count($row_data) - 1){
				$i_cols .= ",";
				$i_vals .= ",";
			}			
		}
		
		$sql .= "($i_cols) VALUES ($i_vals)";
		
		mysql_query($sql) or die(mysql_error() . "<p class=\"error\">$sql</p>");
		
		return $sql;
	
	}
	
	
	/**
	* update
	* update template
	*
	* @param   string   table
	* @param   string   cols (csv)
	* @param   array    values
	* @param   string   key column name
	* @param   string   key value
	*     
	*/
	function update($table,$row_data,$k,$v)
	{
		
		$sql = "UPDATE `$table` SET ";
		$update = "";		
			
		for($i=0;$i<count($row_data);$i++){
			
			$update .= "`".$row_data[$i]['field']."` = '" . mysql_real_escape_string(stripslashes($row_data[$i]['value'])) . "'";
			if($i != count($row_data) - 1){
				$update .= ",";				
			}			
		}
		
		$sql .= "$update WHERE `$k` = '$v'";
		mysql_query($sql) or die(mysql_error() . "<p class=\"error\">$sql</p>");
		
		return $sql;
	
	}
	
	
	/**
	* getInsertId
	* query and return a row in array form
	*
	* @param   string   table
	*     
	* @return  integer  max id from table
	
	BROKEN DO NOT USE
	
	*/
	
	
	function getInsertId($table){
		
		$q = mysql_query("SHOW TABLE STATUS FROM `" . $GLOBALS['DATABASE']['db'] . "` LIKE '" . $table . "'");
		$row = mysql_fetch_assoc($q);
		return intval($row['Auto_increment']);
	
	}
	
	/**
	* generateSearch
	* builds search WHERE sql statement
	*
	* @param   array    fields
	* @param   string   search terms
	*
	* @return  string   WHERE component of sql statement
	*/
	
	function generateSearch($fields,$search)
	{
		$searchQ = "";
		
		for($i=0;$i<count($fields);$i++){
			$searchQ .= '`' . $fields[$i] . "` LIKE " . $search;
			if($i != count($fields) - 1) $searchQ .= " || ";
		}
	
		return $searchQ;
	
	}
	

}
?>