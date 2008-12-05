<?php

	/**
	 * 此类可进行图片缩放，裁切，水印等处理
	 *
	 * @example	   Alp_Image::factory($path_to_image)->resize(....);
	 * @package    Image
	 * @author     Linyupark
	 */

	class Alp_Image
	{
		public $image;
		public $path;
		public $ext; // 原始图后缀
		public $width;
		public $height;
		public $size;
		public $type; // mime提取
		public $allowed_types = array(
			'jpeg','png','gif','jpg'
		);
		
		// 图片资源对象
		protected $image_resource;
		
		# 工厂模式 :::::::::::::::::::::::::::::::::::::::::::::::::::::
		static function init($image)
		{
			return new Alp_Image($image);
		}
		
		function __construct($image)
		{
			if(!extension_loaded("gd"))
			{
				Alp_Sys::exception('Alp_Image类需要GD库支持,请修改php配置');
			}
			if(!file_exists($image))
			{
				Alp_Sys::exception($image.'图片文件不存在');
			}
			else
			{
				$info = getimagesize($image);
				$this->width = $info[0];
				$this->height = $info[1];
				$this->size = filesize($image);
				$type = Alp_String::stripFilePath($info['mime']);
				if(!in_array($type, $this->allowed_types))
				{
					Alp_Sys::exception($type.'类型的图片无法操作');
				}
				else $this->type = $type;
				
				$this->path = pathinfo($image, 1);
				$this->image = Alp_String::stripFileExt(pathinfo($image, 2));
				$this->ext = Alp_String::stripFile(pathinfo($image, 2));
				$this->image_resource = $this->createResource($type, $image);
			}
		}
		
		# 清除图象资源
		private function clean()
		{
			imagedestroy($this->image_resource);
		}
		
		# 创建图象资源 :::::::::::::::::::::::::::::::::::::::::::::::::::::
		function createResource($type, $image)
		{
			$create_function = array(
				'jpg' => 'imagecreatefromjpeg', 
				'jpeg' => 'imagecreatefromjpeg', 
				'png' => 'imagecreatefrompng', 
				'gif' => 'imagecreatefromgif'
			);
			$im = $create_function[$type]($image);
			imagecolortransparent($im, null);
			return $im;
		}
		
		# 获取文件kb ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		function kb()
		{
			return round($this->size / 1024, 1);
		}
		
		# 裁切图片 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		function crop($dst_image_name, $w, $h, $sx = 0, $sy = 0)
		{
			$new_image = imagecreatetruecolor($w, $h);
			imagecopy($new_image, $this->image_resource, 0, 0, $sx, $sy, $w, $h);
			$this->image_resource = $new_image;
			$this->output($dst_image_name);
		}
		
		# 改图尺寸 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		function resize($dst_image_name, $w, $h, $type=null)
		{
			$new_image = imagecreatetruecolor($w, $h);
			imagecopyresampled($new_image, $this->image_resource, 0, 0, 0, 0, $w, $h, $this->width, $this->height);
			$this->image_resource = $new_image;
			$this->output($dst_image_name, $type);
		}
		
		# 添加水印 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		function watermark($png_logo, $position = 'rb', $opacity = 50)
		{
			if (!file_exists($png_logo))
				Alp_Sys::exception($png_logo.'该水印文件不存在');
			
			$dst_image = imagecreatefrompng($png_logo);
			$dst_w = imagesx($dst_image);
			$dst_h = imagesy($dst_image);
			$dst_x = 0;
			$dst_y = 0;
			switch ($position)
			{
				case "lt":
					break;
				case "ct":
					$dst_x = ($this->width - $dst_w) / 2;
					break;
				case "rt":
					$dst_x = $this->width - $dst_w;
					break;
				case "lc":
					$dst_y = ($this->height - $dst_h) / 2;
					break;
				case "cc":
					$dst_x = ($this->width - $dst_w) / 2;
					$dst_y = ($this->height - $dst_h) / 2;
					break;
				case "rc":
					$dst_x = $this->width - $dst_w;
					$dst_y = ($this->height - $dst_h) / 2;
					break;
				case "lb":
					$dst_y = $this->height - $dst_h;
					break;
				case "cb":
					$dst_x = ($this->width - $dst_w) / 2;
					$dst_y = $this->height - $dst_h;
					break;
				case "rb":
					$dst_x = $this->width - $dst_w;
					$dst_y = $this->height - $dst_h;
					break;
			}
			imagecopymerge($this->image_resource, $dst_image, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $opacity);
			$this->output($this->image);
		}
		
		# 文件流输出 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		function stream($quality = 100, $auto_delete = false)
		{
			$stream_function = array(
				'jpg' => 'imagejpeg', 
				'jpeg' => 'imagejpeg', 
				'png' => 'imagepng', 
				'gif' => 'imagegif'
			);
			
			header("Content-type: image/{$this->type}");
			
			if($this->type == 'jpeg')
				$stream_function[$this->type]($this->image_resource, null, $quality);
			else
				$stream_function[$this->type]($this->image_resource); 
			
			// 自动删除原始处理文件
			if($auto_delete == true)
			{
				$delete_image = $this->path.'/'.$this->image.'.'.$this->ext;
				@unlink($delete_image);
			}
			
			$this->clean();
		}
		
		# 文件输出 ::::::::::::::::::::::::::::::::::::::::::::::::::::::::
		function output($dst_image, $type = null, $path = null, $quality = 100, $auto_delete = false)
		{
			$output_function = array(
				'jpg' => 'imagejpeg', 
				'jpeg' => 'imagejpeg', 
				'png' => 'imagepng', 
				'gif' => 'imagegif'
			);
			
			if($type == null || !isset($output_function[$type])) $type = $this->type;
			
			if($path == null) 
				$path = $this->path;
			
			// 输出图片完整地址
			$output_image = $path.'/'.$dst_image.'.'.$type;
				
			if($this->type == 'jpeg')
				$output_function[$type]($this->image_resource, $output_image, $quality);
			else
				$output_function[$type]($this->image_resource, $output_image); 
			
			// 自动删除原始处理文件
			if($auto_delete == true)
			{
				$delete_image = $this->path.'/'.$this->image;
				@unlink($delete_image);
				$delete_image .= '.'.$this->ext;
				@unlink($delete_image);
			}
			
			$this->clean();
		}
	}

?>