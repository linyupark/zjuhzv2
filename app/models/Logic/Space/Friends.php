<?php

	/**
	 * 好友相关逻辑处理类
	 *
	 */
	class Logic_Space_Friends extends DbModel 
	{	
		/**
		 * 返回所有好友的id
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function ids($uid)
		{
			return parent::Space()->fetchAll('SELECT `friend` FROM `tb_friends` WHERE `uid` = ? AND `type` = "pass"', $uid);
		}
		
		/**
		 * 获取指定用户指定好友分类列表信息
		 *
		 * @param unknown_type $uid
		 * @param unknown_type $sort
		 * @return unknown
		 */
		public static function fetch($uid, $sort = 0, $limit = null)
		{
			$select = parent::Space()->select();
			$select->from(array('f' => 'tb_friends'))
				   ->where('f.type = ?', 'pass')->where('f.uid = ?', $uid);
			// 分类筛选
			if($sort != 0)
			$select->where('f.sort = ?', $sort);
			$select->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'f.friend = u.uid', 
							  array('uname' => 'u.username', 'usex' => 'u.sex'));
			if(is_numeric($limit)) $select->limit($limit);
			return $select->query()->fetchAll();
		}
		
		/**
		 * 拒绝好友请求
		 *
		 * @param unknown_type $params
		 */
		public static function reject($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try{
				$db->delete('tb_friends', 'uid = '.$params['sender']);
				$db->delete('tb_msg', 'sender = '.$params['sender'].' AND incept = '.$params['uid'].' AND type = "friend"');
				$db->commit();
				
			} catch (Exception $e){
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
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
		 * 好友请求通过
		 *
		 * @param unknown_type $params
		 */
		public static function pass($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try{
				// 更新对方关系表
				$db->update('tb_friends', array(
					'type' => 'pass',
					'time' => $params['time'],
					'sort' => $params['sort'],
				), 'uid = '.$params['uid'].' AND friend = '.$params['sender']);
				
				// 给对方发送确认信息
				if(Logic_Space_Msg::unique('friend', $params['uid'], $params['sender']) == false)	
				$db->insert('tb_msg', array(
					'type' => 'friend',
					'content' => '系统提示：该用户已经同意了你的好友请求',
					'sender' => $params['uid'],
					'incept' => $params['sender'],
					'time' => $params['time']
				));
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
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
					'time' => $params['time']
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
		 * 直接成为朋友关系
		 *
		 */
		public static function rel($uid_a, $uid_b)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try{
				if(self::hasFriend($uid_a, $uid_b) == false)
				$db->insert('tb_friends' ,array(
					'uid' => $uid_a,
					'friend' => $uid_b,
					'sort' => 0,
					'type' => 'pass',
					'time' => time()
				));
				else 
				$db->update('tb_friends' ,array(
					'type' => 'pass',
					'time' => time()
				), 'uid = '.$uid_a.' AND friend = '.$uid_b);
					
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
		 * 改变好友属分组
		 *
		 * @param unknown_type $uid
		 * @param unknown_type $fid
		 * @param unknown_type $sort
		 */
		public static function sort($uid, $fid, $sort)
		{
			parent::Space()->update('tb_friends', array('sort' => $sort), 'uid = '.$uid.' AND friend = '.$fid);
		}
		
		/**
		 * 删除分组
		 *
		 * @param unknown_type $params
		 */
		public static function delSort($params)
		{
			parent::Space()->delete('tb_friends_sort', 'uid = '.$params['uid'].' AND sid = '.$params['sid']);
		}
		
		/**
		 * 更新分组信息
		 *
		 * @param unknown_type $params
		 */
		public static function updateSort($params)
		{
			parent::Space()->update('tb_friends_sort', array(
				'sid' => $params['sid'],
				'name' => $params['val']
			), 'uid = '.$params['uid']);
		}
		
		/**
		 * 增加新的好友分组
		 *
		 * @param int $sid
		 * @param string $name
		 */
		public static function addSort($params)
		{
			parent::Space()->insert('tb_friends_sort', array(
				'uid' => $params['uid'],
				'sid' => $params['sid'],
				'name' => $params['val']
			));
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