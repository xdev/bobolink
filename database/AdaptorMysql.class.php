<?php

/*

Class: AdaptorMysql

Collection of mysql wrapper functions

*/

class AdaptorMysql implements Db
{
	
	private $connection;
	
	/*
	
	Constructor: __construct
	
	opens connection 
	
	*/
	
	public function __construct()
	{
		$this->openConnection();
	}
	
	/*
	
	Destructor: __destruct
	
	closes connection
	
	*/
	
	public function __destruct()
	{
		$this->closeConnection();
	}
	
	/*
	
	Function: openConnection
	
	opens standard conection to db with $_GLOBALS['DATABASE']
	
	*/
	
	public function openConnection()
	{
		$DB = $GLOBALS['DATABASE'];
				
		$this->connection = mysql_connect($DB['host'], $DB['user'], $DB['pass']);
		
		if (!$this->connection){
			die('Could not connect to the database: ' . mysql_error());
		}
		mysql_select_db($DB['db']);
	}
	
	/*
	
	Function: closeConnection
	
	closes connection
	
	*/
	
	public function closeConnection()
	{
		mysql_close($this->connection);
	}
	
	/*
	
	Function: querySimple
	
	query and return a resource identifier
	
	Parameters:
	
		sql:String - sql query
	
	Returns:
	
		array of mysql resource
	
	*/
	
	public function sql($sql)
	{
		$r = mysql_query($sql) or die(mysql_error() . $sql);
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
	
	public function queryRow($sql)
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
	
	/*
	
	Function: query
	
	query and return a recordset in array form
	
	Parameters:
	
		sql:String - sql query
	
	Returns:
		
		multidimensional associate/numeric array
		
	*/
	
	public function query($sql){
	
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
	
	public function insert($table,$row_data)
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
	
	public function update($table,$row_data,$k,$v)
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
	
	/*
	
	Function: getInsertId
	
	Gets value of auto_increment for a table. BROKEN DO NOT USE!
	
	Parameters:
	
		table:String - table name
	
	Returns:
	
		Number max id from table	
	
	*/
		
	public function getInsertId($table){
		
		$q = mysql_query("SHOW TABLE STATUS FROM `" . $GLOBALS['DATABASE']['db'] . "` LIKE '" . $table . "'");
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
	
	public function generateSearch($fields,$search)
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