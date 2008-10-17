<?php

    class DbModel
    {
        public static function getAdapter($dbname)
        {
            $config = Zend_Registry::get('config');
            $mode = $config->common->dbmode;
            $adapter = $config->common->adapter;
            $params = $config->$mode->params->toArray();
            $params['dbname'] = $dbname;
            return Zend_Db::factory($adapter, $params);
        }
    }

?>