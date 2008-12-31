<?php

	class Alp_String
	{
		# 转换成HTML可显示::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function html($string)
		{
			return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string)));
		}
		
		# 解密::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function decrypt($x_str, $int = 1)
		{
			$str = '';
			$x_str = base64_decode($x_str);
			$len_str = strlen($x_str);
			for($i = 0; $i < $len_str; $i++)
			{
				$x_key = ($i + $len_str) + $int;
				$x_key = ($x_key + 255) % 255;
				$str .= chr(ord(substr($x_str, $i, 1)) ^ $x_key);
			}
			return $str;
		}
		
		# 加密::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function encrypy($str, $int = 1)
		{
			$x_str = '';
			$len_str = strlen($str);
			for($i = 0; $i < $len_str; $i++)
			{
				$x_key = ($i + $len_str) + $int;
				//未知的钥匙值不能大于255
				$x_key = ($x_key + 255) % 255;
				$x_str .= chr(ord(substr($str, $i, 1)) ^ $x_key);
			}
			return base64_encode($x_str);
		}
		
		# utf8的字符串长度截取::::::::::::::::::::::::::::::::::::::::::::::::::
		static function utfSubStr($str, $len)
	    {
	    	for($i=0; $i<$len; $i++)
	        {
	        	$tmp_str = substr($str, 0, 1);
	            if(ord($tmp_str) > 127)
	            {
	            	$i++;
	            	if($i<$len)
	            	{
	            		$new_str[] = substr($str, 0, 3);
	                    $str = substr($str, 3);
	                }
	            }
	            else
	            {
	            	$new_str[] = substr($str, 0, 1);
	            	$str=substr($str, 1);
	            }
	        }
	        return join($new_str);
	    }
	    
	    # 清除文件名中的不安全元素 :::::::::::::::::::::::::::::::::::::::::::
	    static function cleanFileName($filename)
	    {
	    	$bad = array("<!--", "-->", "'", "<", ">", '"', '&', '$', '=', ' ', ';', '?', '/', "%20", "%22", "%3c",  // <
		    "%253c",  // <
		    "%3e",  // >
		    "%0e",  // >
		    "%28",  // (
		    "%29",  // )
		    "%2528",  // (
		    "%26",  // &
		    "%24",  // $
		    "%3f",  // ?
		    "%3b",  // ;
		    "%3d" // =
		    );
		    
		    foreach($bad as $val)
		    {
		      $filename = str_replace($val, '', $filename);
		    }
		    
		    return $filename;
	    }
	    
	    # 过滤路径::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	    static function stripFilePath($string)
	    {
	    	$parts = explode('/', $string);
	    	return array_pop($parts);
	    }
	    
	    # 文件名加后缀::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	    static function appendFileName($string, $v)
	    {
	    	return self::stripFileExt($string).$v.'.'.self::stripFile($string);
	    }
	    
		# 保留后缀名::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	    static function stripFile($string)
	    {
	    	$string = self::stripFilePath($string);
	    	$parts = explode('.', $string);
	    	return array_pop($parts);
	    }
	    
	    # 过滤文件后缀::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function stripFileExt($string)
		{
			$string = self::stripFilePath($string);
			$parts = explode('.', $string);
			return $parts[0];
		}
		
		# 获取随即字母数字::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function random($length = 8)
		{
	      // start with a blank string
	      $string = "";
	    
	      // define possible characters
	      $possible = "0123456789abcdfghjkmnpqrstvwxyz"; 
	        
	      // set up a counter
	      $i = 0; 
	        
	      // add random characters to $string until $length is reached
	      while ($i < $length) { 
	    
	        // pick a random character from the possible ones
	        $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
	            
	        // we don't want this character if it's already in the string
	        if (!strstr($string, $char)) { 
	          $string .= $char;
	          $i++;
	        }
	    
	      }
	    
	      // done!
	      return $string;
	    }
	}

?>