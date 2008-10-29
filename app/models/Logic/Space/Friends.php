<?php

	/**
	 * 好友相关逻辑处理类
	 *
	 */
	class Logic_Space_Friends extends DbModel 
	{
		/**
		 * 增加新的好友分组
		 *
		 * @param int $sid
		 * @param string $name
		 */
		public static function addSort($sid, $name)
		{
			
		}
		
		/**
		 * 指定用户是否包含该好友分组
		 *
		 * @param int $uid
		 * @param int $sid
		 * @return boolean
		 */
		public static function hasSort($uid, $sid)
		{
			$sorts = self::getSort($uid);
			return isset($sorts[$sid]);
		}
		
		/**
		 * 获取指定用户的好友分组数据
		 *
		 * @param int $uid
		 * @return array
		 */
		public static function getSort($uid)
		{
			$default = Zend_Registry::get('config')->friends_sort->sid->toArray();
			$ext = parent::Space()->fetchAll('SELECT `sid`,`name` FROM `tb_friends_sort` WHERE `uid` = ?', $uid);
			
			if(count($ext) > 0)
			{
				foreach ($ext as $v)
				{
					if(!isset($default[$v['sid']]))
					$default += array($v['sid'] => $v['name']);
				}
			}
			
			return $default;
		}
	}

?>