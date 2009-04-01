<?php

	class Alp_Sys
	{
		public static $language = 'Chinese'; // 设置系统语言
		
		private static $messages = array();
		private static $pack = null;
		
		static function conv($key, $params = null, $namespace = null)
		{
			// 使用此函数前需要确认已经调用语言转换包
			$language_location = dirname(__FILE__).'/Lang/'.self::$language.'.php';
			if(self::$pack == null && file_exists($language_location))
			self::$pack = include_once($language_location);
			
			$string = self::$pack[$key];
			if($params != null)
			{
				foreach($params as $k => $v)
				{
					$string = str_replace("%{$k}%", $v, $string);
				}
			}
			if($namespace == null) $namespace = $key;
			return self::$messages[$namespace] = $string;
		}
		
		# 返回所有记录在案的信息 ::::::::::::::::::::::::::::::::::::::::::::::::
		static function allMsg($decorate = '* ', $newline = '<br />')
		{
			if(count(self::$messages) > 0) // 确保有信息
			{
				$tmp = '';
				foreach(self::$messages as $msg)
				{
					$tmp .= $decorate.$msg.$newline;
				}
				return $tmp;
			}
			return null;
		}
		
		# 清除信息数组 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function cleanMsg()
		{
			self::$messages = array();
		}
		
		# 纯信息数组 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function getMsg()
		{
			if(count(self::$messages) == 0)
			return null;
			else return self::$messages;
		}
		
		# 信息数组转换成JSON ::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function getMsgJson()
		{
			if(class_exists('Zend_Json', false))
			{
				return Zend_Json::encode(self::$messages);
			}
			else return json_encode(self::$messages);
		}
		
		# 处理信息 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function msg($key = null, $content = null)
		{
			if($key == null && $content == null) // 获取当前已经存放的所有信息
			{
				return self::$messages;
			}
			elseif($key != null && $content == null) //  获取指定关键字的信息
			{
				return self::$messages[$key];
			}
			else // 存放信息
			{
				self::$messages[$key] = $content;
				return null;
			}
		}
		
		# 强制下载 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function download($filename, $type)
		{
			header("Cache-Control: public");
		    header("Content-Description: File Transfer");
		    header("Content-Disposition: attachment; filename={$filename}");
		    header("Content-Type: {$type}");
		    header("Content-Transfer-Encoding: binary");
		}
		
		# 根据情况抛出异常 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function exception($msg)
		{
			if(class_exists('Zend_Exception', false))
				throw new Zend_Exception($msg);
			else 
				throw new Exception($msg);
		}
		
		# 获取浏览器类型 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function browser()
		{
			$info = $_SERVER['HTTP_USER_AGENT'];
			if (strstr($info, 'MSIE 6.0') != false)
			{
				return 'IE6';
			}
			elseif (strstr($info, 'MSIE 7.0') != false)
			{
				return 'IE7';
			}
			elseif (strstr($info, 'Firefox') != false)
			{
				return 'Firefox';
			}
			elseif (strstr($info, 'Chrome') != false)
			{
				return 'Chrome';
			}
			elseif (strstr($info, 'Safari') != false)
			{
				return 'Safari';
			}
			else return 'unknow';
		}
		
		# 在开放目录下建立新目录 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function mkdir($dir, $mod = '0777')
		{
			return @mkdir($_SERVER['DOCUMENT_ROOT'].$dir, $mod);
		}
		
		# 获取某目录下的dir数据 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function lsdir($dir)
		{
			if(is_dir($dir))
			{
				$arr = scandir($dir);
				$tmp = array();
				foreach ($arr as $i)
				{
					if(is_dir($dir.'/'.$i) && $i != '.' && $i != '..')
					$tmp[] = $i;
				}
				return $tmp;
			}
			return false;
		}
		
		# 获取目录下的文件列表 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function lsfile($dir, $inc_dir = false)
		{
			if(is_dir($dir))
			{
				$arr = scandir($dir);
				$tmp = array();
				foreach ($arr as $i)
				{
					if($i == '.' || $i == '..' || $i == '.svn') continue;
					
					if($inc_dir == false)
					{
						if(!is_dir($dir.'/'.$i)) continue;
					}
					$tmp[] = $i;
				}
				return $tmp;
			}
			return false;
		}
		
		# 软自动跳转 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function jump($url, $sec = 0)
		{
			$sec = $sec * 1000;
			if ($sec == 0)
			return "<script type='text/javascript'>location.href='{$url}';</script>";
			else
			return "<script type='text/javascript'>var t = setTimeout(\"location.href='{$url}'\",{$sec});</script>";
		}
		
		# 获取IP地址 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function ip()
		{
			if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
			{
				$ip = getenv("HTTP_CLIENT_IP");
			}
			elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			{
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			}
			elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
			{
				$ip = getenv("REMOTE_ADDR");
			}
			elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
	   		else
	   		{
	   			$ip = "unknown";
	   		}
	   		return($ip);
	    }
		
		# 获取操作系统 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function os() { 
			$os=""; 
			$Agent = $_SERVER['HTTP_USER_AGENT'];
			if (eregi('win',$Agent) && strpos($Agent, '95')) { 
			$os="Windows 95"; 
			} 
			elseif (eregi('win 9x',$Agent) && strpos($Agent, '4.90')) { 
			$os="Windows ME"; 
			} 
			elseif (eregi('win',$Agent) && ereg('98',$Agent)) { 
			$os="Windows 98"; 
			} 
			elseif (eregi('win',$Agent) && eregi('nt 5.0',$Agent)) { 
			$os="Windows 2000"; 
			} 
			elseif (eregi('win',$Agent) && eregi('nt',$Agent)) { 
			$os="Windows NT"; 
			} 
			elseif (eregi('win',$Agent) && eregi('nt 5.1',$Agent)) { 
			$os="Windows XP"; 
			} 
			elseif (eregi('win',$Agent) && ereg('32',$Agent)) { 
			$os="Windows 32"; 
			} 
			elseif (eregi('linux',$Agent)) { 
			$os="Linux"; 
			} 
			elseif (eregi('unix',$Agent)) { 
			$os="Unix"; 
			} 
			elseif (eregi('sun',$Agent) && eregi('os',$Agent)) { 
			$os="SunOS"; 
			} 
			elseif (eregi('ibm',$Agent) && eregi('os',$Agent)) { 
			$os="IBM OS/2"; 
			} 
			elseif (eregi('Mac',$Agent) && eregi('PC',$Agent)) { 
			$os="Macintosh"; 
			} 
			elseif (eregi('PowerPC',$Agent)) { 
			$os="PowerPC"; 
			} 
			elseif (eregi('AIX',$Agent)) { 
			$os="AIX"; 
			} 
			elseif (eregi('HPUX',$Agent)) { 
			$os="HPUX"; //oSPHP.COM.CN 
			} 
			elseif (eregi('NetBSD',$Agent)) { 
			$os="NetBSD"; 
			} 
			elseif (eregi('BSD',$Agent)) { 
			$os="BSD"; 
			} 
			elseif (ereg('OSF1',$Agent)) { 
			$os="OSF1"; 
			} 
			elseif (ereg('IRIX',$Agent)) { 
			$os="IRIX"; 
			} 
			elseif (eregi('FreeBSD',$Agent)) { 
			$os="FreeBSD"; 
			} 
			if ($os=='') $os = "Unknown"; 
			return $os; 
		} 
	}

?>