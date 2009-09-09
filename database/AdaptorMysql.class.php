<?php

/*

Class: AdaptorMysql

Collection of mysql wrapper functions

*/

//

class AdaptorMysql implements Db
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
		$host     = isset($GLOBALS['DATABASE']['host'])     ? $GLOBALS['DATABASE']['host']     : 'localhost';
		$db       = isset($GLOBALS['DATABASE']['db'])       ? $GLOBALS['DATABASE']['db']       : null;
		$user     = isset($GLOBALS['DATABASE']['user'])     ? $GLOBALS['DATABASE']['user']     : 'root';
		$pass     = isset($GLOBALS['DATABASE']['pass'])     ? $GLOBALS['DATABASE']['pass']     : 'root';
		$charset  = isset($GLOBALS['DATABASE']['charset'])  ? $GLOBALS['DATABASE']['charset']  : 'utf8';
		$timezone = isset($GLOBALS['DATABASE']['timezone']) ? $GLOBALS['DATABASE']['timezone'] : substr(strftime('%z', time()),0,3) . ':' . substr(strftime('%z', time()),3);

		// Connect to database
		if (!self::$connection = mysql_connect($host, $user, $pass)) {
			die('Could not connect to the database: ' . mysql_error());
		}

		// Select database
		if ($db) mysql_select_db($db,self::$connection);

		// Set names (database charset) if charset is defined
		if ($charset) self::sql("SET NAMES '$charset'");

		// Set timezone
		self::sql("SET time_zone = '$timezone'");
	}

	/*

	Function: closeConnection

	closes connection

	*/

	public static function closeConnection()
	{
		mysql_close(self::$connection);
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

	public static function queryRow($sql,$mode=MYSQL_ASSOC)
	{
		$r = mysql_query($sql,self::$connection) or die(mysql_error() . $sql);
		$tA = mysql_fetch_array($r,$mode);

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

	public static function query($sql,$mode=MYSQL_ASSOC){

		$r = mysql_query($sql,self::$connection) or die(mysql_error() . $sql);
		if(mysql_num_rows($r) == 0){
			return array();
		}else{

			$tA = array();
			for($i=0;$i<mysql_num_rows($r);$i++){
				$tA[] = mysql_fetch_array($r,$mode);
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
	
	
	// TODO: return new object, use prepared statements
	public static function insert($table,$row_data)
	{
		//check if first key is numeric, if so format, of course, this prohibits us from having multiple row queries, whatever
		if(array_key_exists(0,$row_data)){
			$row_data = self::formatRows($row_data);
		}				
		$columns = array();
		$values = array();
		foreach($row_data as $key=>$value){
			$columns[] = '`'.$key.'`';
			$values[] = '\''.mysql_real_escape_string(stripslashes($value)).'\'';
		}
		$sql = "INSERT INTO `$table` (".implode(',',$columns).") VALUES (".implode(',',$values).")";
		// can't do this
		mysql_query($sql,self::$connection) or die(mysql_error() . "<p class=\"error\">$sql</p>");
		return $sql;
	}

	public static function formatRows($row_data)
	{
		//converts old school to new school
		
		$tA = array();
		/*
		foreach($row_data as $key=>$value){
			$tA[] = array('field'=>$key,'value'=>$value);
		}
		*/
		foreach($row_data as $row){
			$tA[$row['field']] = $row['value'];
		}
		return $tA;
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
	// TODO: return updated object, use prepared statements
	public static function update($table,$row_data,$k,$v)
	{
		if(array_key_exists(0,$row_data)){
			$row_data = self::formatRows($row_data);			
		}
		$columns = array();		
		foreach($row_data as $key=>$value){
			$columns[] = '`'.$key.'` = \''.mysql_real_escape_string(stripslashes($value)).'\'';
		}
		$sql = "UPDATE `$table` SET ".implode(',',$columns)." WHERE `$k` = '$v'";
		mysql_query($sql,self::$connection) or die(mysql_error() . "<p class=\"error\">$sql</p>");
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

	public static function getInsertId($table)
	{
		$q = mysql_query("SHOW TABLE STATUS FROM `" . $GLOBALS['DATABASE']['db'] . "` LIKE '" . $table . "'",self::$connection);
		$row = mysql_fetch_assoc($q);
		return intval($row['Auto_increment']);
	}

	public static function getPrimaryKey($table)
	{
		$q = self::query("SHOW COLUMNS FROM $table",MYSQL_BOTH);
		//by default set to the first one
		$key = $q[0]['Field'];
		//find real primary key
		foreach($q as $column){
			if($column['Key'] == 'PRI'){
				$key = $column['Field'];
				break;
			}
		}
		return $key;
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
