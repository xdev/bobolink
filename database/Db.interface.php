<?php

/*

$Id$

Interface: Db

For use with a variety of database adaptors

*/

interface Db
{

	public function __construct();
	
	public function __destruct();
	
	public function openConnection();
	
	public function closeConnection();
	
	public function sql($sql);
	
	public function queryRow($sql);
	
	public function query($sql);
	
	public function insert($table,$row_data);
	
	public function update($table,$row_data,$k,$v);
	
	public function getInsertId($table);
	
}

?>