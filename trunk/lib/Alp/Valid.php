<?php

	class Alp_Valid
	{
		private static $__instance;
		
		# 静态获取对象 :::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function load()
		{
			if(self::$__instance == null) 
				return self::$__instance = new Alp_Valid();
			else return self::$__instance;
		}
		
		# 单项校验 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function of($value, $name, $alias, $validator)
		{
			if(get_magic_quotes_gpc())
			{
				$value = stripslashes($value);
			}
			
			// 要用到的校验器(函数)收集
			$validatorArr = explode('|', $validator);
			$match = array();
			foreach ($validatorArr as $fun)
			{
				// eg. fun[arg,arg]|fun[arg,arg]
				if(preg_match("/(.*)[(.*)]/", $fun, $match))
				{
					$fun = $match[1];
					$arg = explode(",", $match[2]);
				}
				
				if(function_exists($fun))
				{
					$value = $fun($value);
				}
				elseif (method_exists(self::load(), $fun))
				{
					self::$fun($name, $value, $alias, $arg);
				}
				else 
				{
					Alp_Sys::exception($fun.' 并非有效的函数, is not an in effect function!');
				}
			}
			
			return $value;
		}
		
		static function strlen_utf8($str)
        {
          $i = 0;
          $count = 0;
          $len = strlen($str);
          while ($i < $len)
          {
            $chr = ord($str[$i]);
            $count++;
            $i++;
            if ($i >= $len)
              break;
            if ($chr & 0x80)
            {
              $chr <<= 1;
              while ($chr & 0x80)
              {
                $i++;
                $chr <<= 1;
              }
            }
          }
          return $count;
        }
		
		# 必填 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	    private function required($name, $value, $alias)
	    {
	    	if(empty($value))
	    	Alp_Sys::conv('valid_required', array($alias), $name);
	    }
	    
		# 必须为数字 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function numeric($name, $value, $alias)
        {
            if(!is_numeric($value))
            Alp_Sys::conv('valid_numeric', array($alias), $name);
        }
        
        # 字母 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function alpha($name, $value, $alias)
        {
            if (!ctype_alpha($value))
            Alp_Sys::conv('valid_alpha', array($alias), $name);
        }
        
        # 字母+数字 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function alnum($name, $value, $alias)
        {
            if(!ctype_alnum($value))
            Alp_Sys::conv('valid_alnum', array($alias), $name);
        }
        
        # 长度范围 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		private function str_between($name, $value, $alias, $arg)
		{
			list($min, $max) = $arg;
			if(self::strlen_utf8($value) > $max || self::strlen_utf8($value) < $min)
			Alp_Sys::conv('valid_str_between', array($alias, $min, $max), $name);
		}
        
        # 数字范围 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function num_between($name, $value, $alias, $arg)
        {
            list($min, $max) = $arg;
            if($value > $max || $value < $min)
            Alp_Sys::conv('valid_num_between', array($alias, $min, $max), $name);
        }
        
        # 固定长度 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function str_exact($name, $value, $alias, $arg)
        {
            if($this->strlen_utf8($value) != $arg[0])
            Alp_Sys::conv('valid_str_exact', array($alias, $arg[0]), $name);
        }
        
        # 相同 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function matches($name, $value, $alias, $arg)
        {
            $input = $arg[0];
            $value2 = $arg[1];
            if($value != $value2)
            Alp_Sys::conv('valid_matches', array($alias, $input), $name);
        }
        
        # 字母+下划线+数字 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function aldash($name, $value, $alias)
        {
            if(!preg_match("/^([-a-z0-9_-])+$/i", $value))
            Alp_Sys::conv('valid_aldash', array($alias), $name);
        }
        
        # 数值相等 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        private function num_equal($name, $value, $alias, $arg)
        {
            $result = $arg[0];
            if($value != $result)
            Alp_Sys::conv('valid_num_equal', array($alias, $result), $name);
        }
	    
        # 有效邮箱 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	    private function valid_email($name, $value, $alias)
	    {
	    	if (!ereg("^([a-zA-Z0-9_])+@([a-zA-Z0-9_])+((.)([a-zA-Z0-9_]))+", $value))
	    	Alp_Sys::conv('valid_email', array($alias), $name);
	    }
	}

?>