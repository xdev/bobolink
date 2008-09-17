<?php

/*

Class: AdaptorPostgresql

Collection of Postgresql wrapper functions

*/

class AdaptorPostgresql implements Db
{
	
	private static $connection;
	private static $instance = null;
	
	/*
	
	Constructor: __construct
	
	opens connection 
	
	*/
	
	private function __construct()
	{
		self::openConnection();
	}
	
	/*
	
	Destructor: __destruct
	
	closes connection
	
	*/
	
	public function __destruct()
	{
		self::closeConnection();
	}
	
	/*
	
	Function: getInstance
	
	Singleton creation
	
	*/
	
	public static function getInstance()
	{
		if(!self::$instance){
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}
	
	/*
	
	Function: openConnection
	
	opens standard conection to db with $_GLOBALS['DATABASE']
	
	*/
	
	public static function openConnection()
	{
		$DB = $GLOBALS['DATABASE'];
			
		self::$connection = pg_connect('host='.$DB['host'].' dbname='.$DB['db'].' user='.$DB['user'].' password='.$DB['pass'])
		    or die('Could not connect: ' . pg_last_error());
		
	}
	
	/*
	
	Function: closeConnection
	
	closes connection
	
	*/
	
	public static function closeConnection()
	{
		pg_close(self::$connection);
	}
	
	/*
	
	Function: querySimple
	
	query and return a resource identifier
	
	Parameters:
	
		sql:String - sql query
	
	Returns:
	
		array of mysql resource
	
	*/
	
	public static function sql($sql)
	{
		$r = mysql_query($sql,self::$connection) or die(mysql_error() . $sql);
		return $r;
	}
	
	/*
	
	Function: queryRow
	
	query and return a row in array form
	
	Parameters:
	
		sql:String - sql query
	
	Returns:
		
		associate/numeric array
		
	*/
	
	public static function queryRow($sql)
	{
		$r = pg_query(self::$connection,$sql) or die('Query failed: ' . pg_last_error());
		if(pg_num_rows($r) == 0){
			return false;
		}else{
			//MYSQL_BOTH
			$tA = pg_fetch_array($r);
			pg_free_result($r);
			return $tA;
		}
	
	}
	
	/*
	
	Function: query
	
	query and return a recordset in array form
	
	Parameters:
	
		sql:String - sql query
	
	Returns:
		
		multidimensional associate/numeric array
		
	*/
	
	public static function query($sql){
		
		$r = pg_query(self::$connection,$sql) or die('Query failed: ' . pg_last_error());
		if(pg_num_rows($r) == 0){
			return false;
		}else{
			//MYSQL_BOTH
			$tA = pg_fetch_all($r);
			pg_free_result($r);
			return $tA;
		}
		
	}
	
	/*
	
	Function: insert
	
	insert template
	
	Parameters:
	
		table:String - table name
		row_data:Array - array of col names and values
	
	Usage:
	
	(start code)
	
	$myA = array();
	
	$myA[] = array('field'=>'colname','value'=>'colvalue');
	...
	
	$db->insert('table_name',$myA);
	
	(end)
	
	Returns:
	
		debug of sql query
	
	*/
	
	public static function insert($table,$row_data)
	{
		
		//need to make exception for mysql functions
		
		$sql = "INSERT INTO $table ";
		
		$i_cols = "";
		$i_vals = "";
			
			
		for($i=0;$i<count($row_data);$i++){
			$i_cols .= "".$row_data[$i]['field']."";
			$i_vals .= "'".  pg_escape_string(self::$connection,stripslashes($row_data[$i]['value'])) . "'";
			if($i != count($row_data) - 1){
				$i_cols .= ",";
				$i_vals .= ",";
			}			
		}
		
		$sql .= "($i_cols) VALUES ($i_vals)";
		
		pg_query(self::$connection,$sql) or die(pg_last_error() . "<p class=\"error\">$sql</p>");
		
		return $sql;
	
	}
	
	
	/*
	
	Function: update
	
	update template
	
	Parameters:
	
		table:String - string name
		row_data:Array - array of column names and values
		k:String - key name
		v:String - key value
	
	Returns:
	
		debug of sql query
	
	*/
	
	public static function update($table,$row_data,$k,$v)
	{
		
		$sql = "UPDATE `$table` SET ";
		$update = "";		
			
		for($i=0;$i<count($row_data);$i++){
			
			$update .= "`".$row_data[$i]['field']."` = '" . pg_escape_string(self::$connection,stripslashes($row_data[$i]['value'])) . "'";
			if($i != count($row_data) - 1){
				$update .= ",";				
			}			
		}
		
		$sql .= "$update WHERE `$k` = '$v'";
		pg_query(self::$connection,$sql) or die(pg_last_error() . "<p class=\"error\">$sql</p>");
		
		return $sql;
	
	}	
	
	/*
	
	Function: getInsertId
	
	Gets value of auto_increment for a table. BROKEN DO NOT USE!
	
	Parameters:
	
		table:String - table name
	
	Returns:
	
		Number max id from table	
	
	*/
		
	public static function getInsertId($table){
		
		$q = mysql_query("SHOW TABLE STATUS FROM `" . $GLOBALS['DATABASE']['db'] . "` LIKE '" . $table . "'",self::$connection);
		$row = mysql_fetch_assoc($q);
		return intval($row['Auto_increment']);
	
	}
	
	/*
	
	Function: generateSearch
	
	builds search WHERE sql statement using LIKE
	
	Parameters:
	
		fields:Array - array of fields
		search:String - string value to search for
	
	Returns:
	
		WHERE component of sql statement

	*/
	
	public static function generateSearch($fields,$search)
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