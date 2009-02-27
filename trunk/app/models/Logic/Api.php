<?php

	class Logic_Api extends DbModel
	{
		public static function alive()
		{
			return time() - 900; // 15分钟等待时间
		}
		
		/**
		 * 返回在线数据
		 *
		 * @return unknown
		 */
		public static function online()
		{
			$alive = self::alive();
			return parent::getSqlite('online.s3db')->fetchAll('SELECT * FROM `ol` WHERE `time` > '.$alive);
		}
		
		/**
		 * 返回在线人数值
		 *
		 * @return unknown
		 */
		public static function onlinex()
		{
			$alive = self::alive();
			$cache = Logic_Cache::factory('Core', 60);
			if(!$row = $cache->load('onlinex'))
			{
				$row = parent::getSqlite('online.s3db')->fetchRow('SELECT COUNT(`uid`) AS numrows FROM `ol` WHERE `time` > '.$alive);
				$cache->save($row);
			}
			return $row['numrows'];
		}
		
		/**
		 * 返回指定uid的用户所有信息
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function user($uid)
		{
			return parent::User()->fetchAll('SELECT * FROM `tb_base` 
				LEFT JOIN `tb_contact` ON `tb_base`.uid = `tb_contact`.uid 
				LEFT JOIN `tb_intro` ON `tb_base`.uid = `tb_intro`.uid 
				LEFT JOIN `tb_privacy` ON `tb_base`.uid = `tb_privacy`.uid 
				LEFT JOIN `tb_edu` ON `tb_base`.uid = `tb_edu`.uid 
				LEFT JOIN `tb_career` ON `tb_base`.uid = `tb_career`.uid 
				WHERE `tb_base`.uid = ?', $uid);
		}
	}

?>