<?php

	/**
	 * 全局数据库指令类
	 *
	 */
    class DbModel
    {
    	/**
    	 * 获取数据库适配器
    	 *
    	 * @param string $dbname database name
    	 * @return object
    	 */
        public static function getAdapter($dbname)
        {
            if(!Zend_Registry::isRegistered('db_'.$dbname))
            {
            	$config = Zend_Registry::get('config');
            	$mode = $config->common->dbmode;
	            $adapter = $config->common->adapter;
	            $params = $config->$mode->params->toArray();
	            $params['dbname'] = $dbname;
	            Zend_Registry::set('db_'.$dbname, Zend_Db::factory($adapter, $params));
            }
            return Zend_Registry::get('db_'.$dbname);
        }
        
        /**
         * 获取SQLITE适配器对象
         *
         * @param string $dbname
         * @return object
         */
        public static function getSqlite($dbname)
        {
        	return Zend_Db::factory('PDO_SQLITE', array('dbname'=>SQLITEROOT.$dbname));
        }
        
        /**
         * 获取用户数据库适配器
         *
         * @return object
         */
        public static function User()
        {
        	return self::getAdapter('zjuhzv2_user');
        }
        
        /**
         * 空间数据库适配器
         *
         * @return object
         */
        public static function Space()
        {
        	return self::getAdapter('zjuhzv2_space');
        }
        
        /**
         * 获取记录数据库适配器
         *
         * @return object
         */
        public static function Log()
        {
        	return self::getAdapter('zjuhzv2_log');
        }
    }

?>