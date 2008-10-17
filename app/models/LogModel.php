<?php

    class LogModel
    {
        private static $_dao;
        
        protected static function dao()
        {
            $dao = self::$_dao;
            if($dao == null) $dao = DbModel::getAdapter('zjuhzv2_log');
            return $dao;
        }
    }

?>