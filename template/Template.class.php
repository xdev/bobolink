<?php

/*

$Id$

Class: Template

Collection of general template functions 

*/

class Template
{
	
	public $db;
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
	}
	
	/*
	
	Function: siteConfig
	
	Defines contants stored in the database config table
	
	Parameters:
	
		table:String - config table
	
	*/
	
	public function siteConfig(
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
	
	Function: requestUri
	
	Strip any queries from the request URI and return an array
	
	Returns:
	
		array
	
	*/
	
	public function requestUri()
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
	
	public function pageId(
		$parentId=0,
		$level=0,
		$id=0
	)
	{
		if (isset($this->requestUri[$level])) {
			if ($q = $this->db->queryRow("
				SELECT id,title,slug,parent_id
				FROM site_map
				WHERE active = '1'
					AND parent_id = '".$parentId."'
					AND slug IN ('".$this->requestUri[$level]."','*')
			")) {
				if ($q['slug'] == '*') return $q['id'];
				else return $this->pageId($q['id'],$level+1,$q['id']);
			}
		} else {
			return $id;
		}
	}
	
	/*
	
	Function: pageUri
	
	Determine the DB page uri for the requested page id
	
	Parameters:
	
		id:Integer - page id
		uri:string - the current page uri
	
	Returns:
	
		string
	
	*/
	
	public function pageUri(
		$id=0,
		$uri=''
	)
	{
		if ($id && $q = $this->db->queryRow("
			SELECT id,title,slug,parent_id
			FROM site_map
			WHERE active = '1'
				AND id = '".$id."'
		")) {
			if ($q['slug'] == '*') return $uri;
			else return $this->pageUri($q['parent_id'],$q['slug'].($uri ? '/' : '').$uri);
		} else {
			return '/'.$uri;
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
		if ($this->page['id']) {
			// Set html title
			if ($this->page['title']) {
				$r = $position ? $site_name.$separator.$this->page['title'] : $this->page['title'].$separator.$site_name;
			} else {
				$r = $site_name;
			}
		} elseif ($this->error404) {
			if ($this->page['title']) {
				$r = $position ? $site_name.$separator.$this->page['title'] : $this->page['title'].$separator.$site_name;
			} else {
				$r = $position ? $site_name.$separator.'Error 404' : 'Error 404'.$separator.$site_name;
			}
		} else {
			// If homepage, determine which title to use
			$r = $home_title ? $home_title : $site_name;
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
				<script type="text/javascript">
					var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
					document.write(unescape("%%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%%3E%%3C/script%%3E"));
				</script>
				<script type="text/javascript">
					var pageTracker = _gat._getTracker("%s");
					pageTracker._initData();
					pageTracker._trackPageview();
				</script>',
				GOOGLE_ANALYTICS
			);
		}
	}
	
}