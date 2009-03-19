<?php

	class Logic_Space_Bar_Events extends DbModel
	{	
		/**
		 * 取消报名
		 *
		 * @param unknown_type $tid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function unsign($tid, $uid)
		{
			$members = self::members($tid);
			if($members == false) return false;
			else
			{
				 unset($members[$uid]);
				 parent::Space()->update('tb_events', array('member' => serialize($members)), 'tid = '.$tid);
				 return true;
			}
		}
		
		/**
		 * 报名
		 *
		 * @param unknown_type $tid
		 * @param unknown_type $uid
		 * @param unknown_type $username
		 */
		public static function sign($tid, $uid, $username)
		{
			$members = self::members($tid);
			if($members == false) $members = array($uid => $username);
			else $members[$uid] = $username;
			parent::Space()->update('tb_events', array('member' => serialize($members)), 'tid = '.$tid);
		}
		
		/**
		 * 获取报名成员
		 *
		 * @param unknown_type $tid
		 * @return unknown
		 */
		public static function members($tid)
		{
			$row = parent::Space()->fetchRow('SELECT `member` FROM `tb_events` WHERE `tid` = ?', $tid);
			return unserialize($row['member']);
		}
		
		/**
		 * 修改活动帖
		 *
		 */
		public static function mod($params, $tid)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				
				$db->update('tb_tbar', array(
					'title' => $params['title'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				), 'tid = '.$tid);
				
				$db->update('tb_events', array(
					'content' => $params['content'],
					'modtime' => time(),
					'limit' => $params['limit'],
					'time' => $params['time'],
					'address' => $params['address']
				), 'tid = '.$tid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 查看活动
		 *
		 * @param unknown_type $tid
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('e' => 'tb_events'), 'bar.tid = e.tid');
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname','u.sex'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 发布求助贴
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function insert($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_tbar', array(
					'type' => $params['type'],
					'title' => $params['title'],
					'puber' => Cmd::uid(),
					'pubtime' => time(),
					'replytime' => time(),
					'group' => $params['group'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				));
				$tid = $db->lastInsertId();
				$db->insert('tb_events', array(
					'tid' => $tid,
					'content' => $params['content'],
					'limit' => $params['limit'],
					'time' => $params['time'],
					'address' => $params['address']
				));
				$db->commit();
				return $tid;
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
	} 

?>