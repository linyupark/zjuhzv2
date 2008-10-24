<?php

    /**
     * 全局通用指令
     */
    class Cmd
    {
        # 设置SESSION
        static function setSess($key, $data)
        {
            Zend_Registry::get('sess')->$key = $data;
        }
        
        # 获取SESSION
        static function getSess($key, $index = null)
        {
            if($index == null)
            {
                return Zend_Registry::get('sess')->$key;
            }
            else return Zend_Registry::get('sess')->$key[$index];
        }
    }

?>