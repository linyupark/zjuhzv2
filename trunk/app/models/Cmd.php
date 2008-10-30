<?php

    /**
     * 全局通用指令
     *
     */
    class Cmd
    {
    	/**
    	 * 获取自己的用户id
    	 *
    	 * @return int
    	 */
    	public static function uid()
    	{
    		return (int)self::getSess('profile', 'uid');
    	}
    	
        /**
         * 设置session
         *
         * @param string $key 标识符
         * @param string/array $data 数据
         */
        public static function setSess($key, $data)
        {
            Zend_Registry::get('sess')->$key = $data;
        }
        
        /**
         * 获取sesssion
         *
         * @param string $key 标识符
         * @param string $index 数组索引
         * @return unknown
         */
        public static function getSess($key, $index = null)
        {
            if($index == null)
            {
                return Zend_Registry::get('sess')->$key;
            }
            else
            {
            	$data_set = Zend_Registry::get('sess')->$key;
            	return $data_set[$index];
            } 
        }
    }

?>