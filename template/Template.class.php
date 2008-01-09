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
	
	public function __construct($db=null)
	{
		require_once LIB . 'database/Db.interface.php';
		require_once LIB . 'database/AdaptorMysql.class.php';
		$this->db = $db ? $db : new AdaptorMysql();
		$this->siteConfig();
		$this->page();
	}
	
	/*
	
	Function: siteConfig
	
	Defines contants stored in the database config table
	
	Parameters:
	
		table:String - config table
	
	*/
	
	private function siteConfig(
		$table = 'site_config'
	)
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
			$this->page['title'] = 'Error 404';
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
				$this->page['tpl'] = '_generic';
				$this->page['tpl_file'] = $_SERVER['DOCUMENT_ROOT'].'/'.$tpl_path.'/_generic.php';
			}
		} else {
			// If the page doesn't exist, use the Error 404 tpl file
			$this->page['tpl'] = '_error404';
			$this->page['template'] = '1';
			$this->page['tpl_file'] = $_SERVER['DOCUMENT_ROOT'].'/'.$tpl_path.'/_error404.php';
		}
		// If homepage, define other necessary page variables
		if ($this->page['tpl'] == 'home') {
			$this->page['template'] = 1;
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
	
	private function pageId(
		$parentId=0,
		$level=0,
		$id=0
	)
	{
		if ($this->requestUri[0] == '') {
			return -1;
		} else {
			if (isset($this->requestUri[$level])) {
				if ($q = $this->db->queryRow("
					SELECT id,title,slug,parent
					FROM site_map
					WHERE active = '1'
						AND parent = '".$parentId."'
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
	
	Function: file
	
	Determine which template file to use
	
	Returns:
	
		string
	
	*/
	
	public function file()
	{
		if (isset($this->page['template']) && $q = $this->db->queryRow("
			SELECT file
			FROM site_templates
			WHERE active = '1'
				AND id = '".$this->page['template']."'
		")) {
			return 'php/templates/'.$q['file'].'.php';
		} else {
			die('Template not found.');
		}
	}
	
	/*
	
	Function: htmlTitle
	
	Create the appropriate title for the <head> section
	
	Parameters:
	
		site_name:String - Name of the website
		separator:String - string to separate site name from page title
		position:Numeric - (0) to put page title before site name, (1) for after
		home_title:String - optional/alternate page title for homepage
	
	Returns:
	
		formatted string
	
	*/
	
	public function htmlTitle(
		$site_name = 'Site Name',
		$separator = ' | ',
		$position = 1,
		$home_title = null
	)
	{
		if ($this->page['id'] == -1) {
			// If homepage, determine which title to use
			$r = $home_title ? $home_title : $site_name;
		} else {
			// Set html title
			if ($this->page['title']) {
				$r = $position ? $site_name.$separator.$this->page['title'] : $this->page['title'].$separator.$site_name;
			} else {
				$r = $site_name;
			}
		}
		return $r;
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