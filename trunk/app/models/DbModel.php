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
            $config = Zend_Registry::get('config');
            $mode = $config->common->dbmode;
            $adapter = $config->common->adapter;
            $params = $config->$mode->params->toArray();
            $params['dbname'] = $dbname;
            return Zend_Db::factory($adapter, $params);
        }
        
        /**
         * 获取SQLITE适配器对象
         *
         * @param string $dbname
         * @return object
         */
        public static function getSqlite($dbname)
        {
        	return Zend_Db::factory('PDO_SQLITE', array('dbname'=>$_SERVER['DOCUMENT_ROOT'].'/sqlite/'.$dbname));
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