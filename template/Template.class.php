<?php

/*

$Id$

Class: Template

Collection of general template functions

*/

class Template
{
	
	private $db;
	public $requestUri;
	
	public $error404;
	public $id;
	
	public $page;
	public $pageClass;
	
	public function __construct()
	{
		require_once LIB . 'database/Db.interface.php';
		require_once LIB . 'database/AdaptorMysql.class.php';
		$this->db = new AdaptorMysql();
		$this->siteConfig();
		$this->page();
	}
	
	/*
	
	Function: siteConfig
	
	Defines contants stored in the database config table
	
	Parameters:
	
		table:String - config table
	
	*/
	
	private function siteConfig($table = 'site_config')
	{
		if ($this->db->query("
			SHOW TABLES LIKE '".$table."'
		")) {
			if ($q = $this->db->query("
				SELECT *
				FROM ".$table."
				WHERE active = '1'
			")) {
				foreach ($q as $row) {
					if (!defined($row['name'])) define($row['name'],$row['value']);
				}
			}
		}
	}
	
	/*
	
	Function: page
	
	Load page into memory
	
	*/
	
	private function page()
	{
		// Get URI request and put it into an array
		$this->requestUri = $this->requestUri();
		// Check URL to make sure it exists
		if ($this->error404 = ($this->page['id'] = $this->pageId()) ? false : true) {
			$this->page['name'] = 'Error 404';
			header('HTTP/1.0 404 Not Found');
		}
		// Set TPL path
		$tpl_path = defined('TPL') ? TPL : 'php/tpl';
		// Load page variables
		if ($this->page['id'] < 0 || ($this->page['id'] && $this->page = $this->db->queryRow("
			SELECT *
			FROM ".TABLE_SITE_MAP."
			WHERE active = '1'
				AND id = '".$this->page['id']."'
		"))) {
			// Determine which TPL file to use
			if ($this->page['id'] < 0 || !$this->page['tpl']) $this->page['tpl'] = $this->requestUri[0] ? implode('.',$this->requestUri) : 'home';
			// If actual TPL file doesn't exist, use a generic one
			if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$tpl_path.'/'.$this->page['tpl'].'.php')) {
				$this->page['tpl_file'] = $_SERVER['DOCUMENT_ROOT'].'/'.$tpl_path.'/'.$this->page['tpl'].'.php';
			} else {
				$this->page['tpl_file'] = $_SERVER['DOCUMENT_ROOT'].'/'.$tpl_path.'/_generic.php';
			}
		} else {
			// If the page doesn't exist, use the Error 404 tpl file
			$this->page['tpl_file'] = $_SERVER['DOCUMENT_ROOT'].'/'.$tpl_path.'/_error404.php';
		}
	}
	
	/*
	
	Function: requestUri
	
	Strip any queries from the request URI and return an array
	
	Returns:
	
		array
	
	*/
	
	private function requestUri()
	{
		$uri = (($qsa = strpos($_SERVER['REQUEST_URI'],'?')) ? substr($_SERVER['REQUEST_URI'],0,$qsa) : $_SERVER['REQUEST_URI']);
		$uri = explode("/",$uri);
		$base_uri = explode("/",substr($_SERVER['PHP_SELF'],1,-(strlen('index.php') + 1)));
		array_splice($uri,0,count($base_uri));
		return $uri;
	}
	
	/*
	
	Function: pageId
	
	Determine the DB page id for the requested URL
	
	Parameters:
	
		parentId:Integer - Parent Id to start from
		level:Integer - how deep the request is
		id:Integer - the current page id
	
	Returns:
	
		integer
	
	*/
	
	private function pageId($parentId=0,$level=0,$id=0)
	{
		if ($this->requestUri[0] == '') {
			return -1;
		} else {
			if (isset($this->requestUri[$level])) {
				if ($q = $this->db->queryRow("
					SELECT id,name,slug,parent_id
					FROM site_map
					WHERE active = '1'
						AND parent_id = '".$parentId."'
						AND slug IN ('".$this->requestUri[$level]."','*')
				")) {
					if ($q['slug'] == '*') return $q['id'];
					else return $this->pageId($q['id'],$level+1,$q['id']);;
				}
			} else {
				return $id;
			}
		}
	}
	
	/*
	
	Function: googleAnalytics
	
	If a google analytic account is defined, this will return the html javascript markup to log page stats
	
	Parameters:
	
		table:String - config table
	
	Returns:
	
		formatted string
	
	*/
	
	public function googleAnalytics()
	{
		if (defined('GOOGLE_ANALYTICS')) {
			return sprintf(
				'
	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
	<script type="text/javascript">
	_uacct = "%s";
	urchinTracker();
	</script>',
				GOOGLE_ANALYTICS
			);
		}
	}
	
}