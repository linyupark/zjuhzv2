<?php
	
	set_include_path('.' . PATH_SEPARATOR . get_include_path() 
						 . PATH_SEPARATOR . '../lib'
						 . PATH_SEPARATOR . '../app/sapce'
						 . PATH_SEPARATOR . '../app/models');
	
	require_once 'Zend/Loader.php';
	Zend_Loader::registerAutoload();
	
	Zend_Session::start();
	Zend_Registry::set('sess', new Zend_Session_Namespace('zjuhz'));
	
	$config = new Zend_Config_Ini('../db/config.ini');
	Zend_Registry::set('config', $config);
	
	$controller = Zend_Controller_Front::getInstance();
	$modules = Alp_Sys::lsdir('../app/space');

	foreach($modules as $mod)
	{
		$controller->addControllerDirectory('../app/space/'.$mod.'/controllers', 'space_'.$mod);
	}
	$controller->addControllerDirectory('../app/public/controllers', 'public');
	$controller->addControllerDirectory('../app/console/controllers', 'console');
	$controller->registerPlugin(new Plugins_Layout());
	$controller->registerPlugin(new Plugins_Acl());
	$controller->setDefaultModule('public');
	$controller->throwExceptions(false);
	$controller->dispatch();