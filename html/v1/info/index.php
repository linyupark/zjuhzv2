<?php
	
	define('V1DOOT', '/usr/local/www/apache22/data/zjuhz');	

	set_include_path(get_include_path().
				PATH_SEPARATOR.V1DOOT.'/common/'.
				PATH_SEPARATOR.V1DOOT.'/common/Custom/'.
				PATH_SEPARATOR.V1DOOT.'/application/info/models/');

	
	require_once 'Zend/Controller/Front.php';

	
	Zend_Loader::registerAutoload();
	
	$iniInfo = new Zend_Config_Ini('Ini/Info.ini');
	$iniDb = new Zend_Config_Ini('Ini/Db.ini');
	$params = $iniDb->default->params->toArray();
	$params['dbname'] = $iniInfo->db->params->dbname;
	$dbInfo = Zend_Db::factory($iniDb->default->adapter, $params);
	Zend_Registry::set('dbInfo', $dbInfo);
    Zend_Registry::set('iniInfo', $iniInfo);
	Zend_Registry::set('sessInfo', new Zend_Session_Namespace('info'));
	Zend_Registry::set('sessCommon', new Zend_Session_Namespace('common'));
	
	Zend_Db_Table::setDefaultAdapter($dbInfo);
	
	/* Layout */
	Zend_Layout::startMvc(array('layoutPath' => V1DOOT.'/application/layouts/', 'layout' => 'info'));
	
	/** run */
	$front = Zend_Controller_Front::getInstance();
	$front->throwExceptions(true)
	      ->registerPlugin(new InfoAcl(Zend_Registry::get('sessCommon')))
	      ->setControllerDirectory(V1DOOT.'/application/info/controllers/')
	      ->dispatch();
        