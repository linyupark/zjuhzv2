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
	}

?>