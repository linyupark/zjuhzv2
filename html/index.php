<?php
	
	set_include_path('.' . PATH_SEPARATOR . get_include_path() 
						 . PATH_SEPARATOR . '../lib'
						 . PATH_SEPARATOR . '../app/sapce'
						 . PATH_SEPARATOR . '../app/models');
	
	date_default_timezone_set('Asia/Shanghai');					 
	
	define('DOMAIN', 'www.zjuhz.com');
	define('HTMLROOT', $_SERVER['DOCUMENT_ROOT']);
	define('SQLITEROOT', '../db/sqlite/');
	define('UPLOADROOT', $_SERVER['DOCUMENT_ROOT'].'/upload/');
						 
	require_once 'Zend/Loader.php';
	Zend_Loader::registerAutoload();
	Zend_Session::start();
	Zend_Registry::set('sess', new Zend_Session_Namespace('zjuhz'));
	
	$config = new Zend_Config_Ini('../db/config.ini');
	Zend_Registry::set('config', $config);
	
	$controller = Zend_Controller_Front::getInstance();
	$space_mods = Alp_Sys::lsdir('../app/space');
	$addon_mods = Alp_Sys::lsdir('../app/addon');
	$api_mods = Alp_Sys::lsdir('../app/api');

	foreach($space_mods as $mod)
	$controller->addControllerDirectory('../app/space/'.$mod.'/controllers', 'space_'.$mod);
	
	foreach($addon_mods as $mod)
	$controller->addControllerDirectory('../app/addon/'.$mod.'/controllers', 'addon_'.$mod);
	
	foreach($api_mods as $mod)
	$controller->addControllerDirectory('../app/api/'.$mod.'/controllers', 'api_'.$mod);

	$controller->addControllerDirectory('../app/public/controllers', 'public');
	$controller->addControllerDirectory('../app/console/controllers', 'console');
	
	$controller->registerPlugin(new Plugins_Layout());
	$controller->registerPlugin(new Plugins_Acl());
	
	$controller->setDefaultModule('public');
	
	$controller->throwExceptions(false);
	$controller->dispatch();