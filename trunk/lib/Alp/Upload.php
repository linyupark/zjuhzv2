<?php

	/**
	 * PHP library (Alp) for Zend Framework
	 * 文件上传处理，需要Alp_String,Alp_Sys类
	 *
	 * @author linyupark@gmail.com
	 * @example 
	 * 
	 * HTML:
	 * <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
		<form method="post" enctype="multipart/form-data">
			<input type="file" name="image[]" /><br />
			<input type="file" name="image[]" /><br />
			<input type="file" name="image[]" /><br />
			<input type="file" name="image[]" />
			<input type="submit" /> 
		</form>
	 * 
	 * PHP:
	 * Alp_Upload::init(array('type'=>'txt|jpg', 'maxsize'=>3, 'random'=>false));
		if(Alp_Upload::isUploaded('image'))
		{
			if(Alp_Upload::handle('image'))
			echo 'done!';
			else echo Alp_Sys::msg('alp_upload_error');
		}
	 */

	class Alp_Upload
	{
		public static $filename = array();
		
		// 上传默认参数
		private static $config = array(
			'maxsize'	=> 100,
			'filename'	=> array(),
			'type'		=> 'jpg|gif|png',
			'overwrite'	=> false,
			'random'	=> false,
			'path'		=> './upload/'
		);
		
		# 初始化上传参数 :::::::::::::::::::::::::::::::::::::::::::::::::::
		static function init($config)
		{
			if(!is_array($config))
			{
				Alp_Sys::exception('上传的设置参数必须为数组');
			}
			
			// 加载默认参数
			$upload_config = self::$config;
			
			// 替换具体参数
			foreach ($config as $k => $v)
			{
				if(!array_key_exists($k, $upload_config))
					Alp_Sys::exception('上传参数键值'.$k.'不存在,请核对');
				$upload_config[$k] = $v;
			}
			
			self::$config = $upload_config;
		}
		
		# 处理上传文件 :::::::::::::::::::::::::::::::::::::::::::::::::::
		static function handle($key)
		{
			if(self::isUploaded($key)) // 确保有文件上传
			{
				self::$filename = $_FILES[$key]['name']; // 存放原始文件名,祛除重复文件
				
				if(self::validPath() && self::validExt($key) && self::validSize($key)) // 校验通过
				{
					$filename = array(); // 最后确定文件名的数组
					
					foreach(self::$filename as $k => $f)
					{
						if($f == '') continue; // 空文件忽略
						
						if(isset(self::$config['filename'][$k])) // 设置对应的文件名
						{
							$filename[$k] = self::setFileName(self::$config['filename'][$k]);
						}
						
						else $filename[$k] = self::setFileName($f);
					}
					
					// 开始转移到指定目录下
					foreach ($_FILES[$key]['tmp_name'] as $k => $tmp)
					{
						if($tmp == '') continue;
						if(!@move_uploaded_file($tmp, self::$config['path'].$filename[$k]))
						{
							Alp_Sys::conv('upload_file_partial');
							return false;
						}
					}
					
					return true;
				}
			}
			return false;
		}
		
		# 设置最后上传的文件名 ::::::::::::::::::::::::::::::::::::::::::::
		static function setFileName($filename)
		{
			$filename = Alp_String::cleanFileName($filename); // 修正上传文件名
			
			if(self::$config['random'] == true) // 目标文件名为随机
			{
				return date('ymdHis').Alp_String::random(12).'.'.Alp_String::stripFile($filename);
			}
			
			if(self::$config['overwrite'] == true) // 覆盖原文件
			{
				return $filename;
			}
			else // 发现同名文件则加后缀
			{
				if(file_exists(self::$config['path'].$filename))
				{
					$name = Alp_String::stripFileExt($filename);
					$ext = Alp_String::stripFile($filename);
					
					for($i = 1; $i < 100; $i++)
					{
						$new_filename = $name.$i.'.'.$ext;
						if(!file_exists(self::$config['path'].$new_filename))
						return $new_filename;
					}
				}
			}
			
			return $filename;
		}
		
		# 上传目标路径 :::::::::::::::::::::::::::::::::::::::::::::::::::
		static function validPath()
		{
			$path = self::$config['path'];
			if(!is_dir($path))
			{
				Alp_Sys::conv('upload_path_invalid', array($path));
				return false;
			}
			
			return true;
		}
		
		# 上传的文件大小是否符合要求 ::::::::::::::::::::::::::::::::::::::
		static function validSize($key)
		{
			$conifg_size = self::$config['maxsize']*1000; // 转换成字节
			$size_array = $_FILES[$key]['size']; // 上传文件的大小数组
			
			foreach($size_array as $size)
			{
				if($size > $conifg_size)
				{
					Alp_Sys::conv('upload_file_exceeds_limit');
					return false;
				}
			}
			
			return true;
		}
		
		# 上传文件的类型符合要求否 ::::::::::::::::::::::::::::::::::::::
		static function validExt($key) 
		{
			$name_array = $_FILES[$key]['name']; // 选择文件名数组
			$allowed_type = self::$config['type'];
			$type_array = explode('|', $allowed_type); // 允许上传文件类型数组
			
			$empty = 0;
			
			// 上传的文件类型校验
			foreach($name_array as $name)
			{
				if($name == '')  // 跳过空的
				{
					$empty ++ ;
					continue;
				}
				
				$ext = Alp_String::stripFile($name);
				
				if(!in_array($ext, $type_array)) // 不允许类型
				{
					Alp_Sys::conv('upload_file_type_invalid', array($name));
					return false;
				}
			}
			
			// 全为空提示选择上传
			if($empty == count($name_array))
			{
				Alp_Sys::conv('upload_no_file_selected');
				return false;
			}
			
			return true;
		}
		
		# 判断文件是否上传 ::::::::::::::::::::::::::::::::::::::::::::::::
		static function isUploaded($key)
		{
			if($_FILES[$key] && !empty($_FILES[$key]['tmp_name']))
		    {
		        return true;
		    }
		    return false;
		}
	}

?>