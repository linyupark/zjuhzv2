<?php

	/**
	 * 好友相关逻辑处理类
	 *
	 */
	class Logic_Space_Friends extends DbModel 
	{
		/**
		 * 返回指定的uid_b是否为uid_a的好友
		 *
		 * @param unknown_type $myid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function hasFriend($uid_a, $uid_b)
		{
			return parent::Space()->fetchRow('SELECT `friend` FROM `tb_friends` WHERE `uid` = ? AND `friend` = ?',array($uid_a, $uid_b));
		}
		
		/**
		 * 返回所有指定UID的好友
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function fetchAll($uid)
		{
			return parent::Space()->fetchAll('SELECT `friend` FROM `tb_friends` WHERE `uid` = ?', $uid);
		}
		
		/**
		 * 增加新的好友分组
		 *
		 * @param int $sid
		 * @param string $name
		 */
		public static function addSort($sid, $name)
		{
			return false;
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