<?php

/*

Interface: Db

For use with a variety of database adaptors

*/

interface Db
{
	
	public static function getInstance();
	
	public static function openConnection();
	
	public static function closeConnection();
	
	public static function sql($sql);
	
	public static function queryRow($sql);
	
	public static function query($sql);
	
	public static function insert($table,$row_data);
	
	public static function update($table,$row_data,$k,$v);
	
	public static function getInsertId($table);
	
}