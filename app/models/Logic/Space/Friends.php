<?php

	/**
	 * 好友相关逻辑处理类
	 *
	 */
	class Logic_Space_Friends extends DbModel 
	{	
		/**
		 * 解除黑名单状态
		 *
		 * @param unknown_type $uid_a
		 * @param unknown_type $uid_b
		 */
		public static function unblock($uid_a, $uid_b)
		{
			parent::Space()->update('tb_friends', array(
				'type' => 1,
			), '`uid` = '.$uid_a.' AND `friend` = '.$uid_b);
		}
		
		/**
		 * 增加好友申请
		 *
		 */
		public static function add($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try{
				// 关系表
				$db->insert('tb_friends', array(
					'uid' => $params['sender'],
					'friend' => $params['incept'],
					'sort' => 0,
					'type' => 'wait',
					'time' => time()
				));
				// 系统站内信发送
				$db->insert('tb_msg', array(
					'type' => $params['type'],
					'content' => $params['content'],
					'sender' => $params['sender'],
					'incept' => $params['incept'],
					'time' => $params['time']
				));
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			
		}
		
		/**
		 * 返回指定的uid_b是否为uid_a的好友
		 *
		 * @param unknown_type $myid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function hasFriend($uid_a, $uid_b)
		{
			$row = parent::Space()->fetchRow('SELECT `type` FROM `tb_friends` WHERE `uid` = ? AND `friend` = ?',array($uid_a, $uid_b));
			return $row ? $row['type'] : false;
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