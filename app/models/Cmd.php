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
    	
    	public static function role()
    	{
    		return self::getSess('profile', 'role');
    	}
    	
    	public static function rolename()
    	{
    		$role = self::role();
    		return Zend_Registry::get('config')->user_role->$role;
    	}
    	
    	/**
    	 * 返回用户的头像
    	 *
    	 * @param unknown_type $uid
    	 * @param unknown_type $size
    	 * @param unknown_type $sex
    	 * @return unknown
    	 */
    	public static function userhead($uid, $size = '40', $sex = '男')
    	{
    		if($sex == '男') $sex = 'male';
    		else $sex = 'female';
    		$dir = UPLOADROOT.'users/head/'.$uid;
    		if(file_exists($dir.'/'.$size.'.gif'))
    		return '/upload/users/head/'.$uid.'/'.$size.'.gif?t='.time();
    		else return Alp_Url::img('v1/'.$sex.'_'.$size.'.gif');
    	}
    	
        /**
         * 设置session
         *
         * @param string $key 标识符
         * @param string/array $data 数据
         */
        public static function setSess($key, $data = null)
        {
            if(is_array($data))
            {
            	$datas = Zend_Registry::get('sess')->$key;
            	if($datas == null)
            	Zend_Registry::get('sess')->$key = $data;
            	else {
            		$result = array_merge($datas, $data);
            		Zend_Registry::get('sess')->$key = $result;
            	}
            }
            elseif($data == null)
            {
            	Zend_Registry::get('sess')->$key = null;
            }
            else
            {
            	Zend_Registry::get('sess')->$key = $data;
            }
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
        
        public static function fck($key, $content = null, $h='320px', $w='100%', $toolbar = 'ZjuhzFront')
        {
        	return '<textarea id="'.$key.'" name="'.$key.'">'.$content.'</textarea>
        			<script type="text/javascript" src="/fck/fckeditor.js"></script>
        			<script type="text/javascript">
        				var oFCKeditor = new FCKeditor("'.$key.'");
        				oFCKeditor.BasePath	= "/fck/";
        				oFCKeditor.ToolbarSet = "'.$toolbar.'";
        				oFCKeditor.Width = "'.$w.'";
        				oFCKeditor.Height = "'.$h.'";
        				oFCKeditor.ReplaceTextarea();
        			</script>';
        }
    }

?>