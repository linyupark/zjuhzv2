<?php

	class Logic_Space_Bar extends DbModel 
	{
		public static function unique($title)
		{
			return parent::Space()->fetchRow('SELECT `tid` FROM `tb_tbar` WHERE `title` = ?', $title);
		}
	}

?>