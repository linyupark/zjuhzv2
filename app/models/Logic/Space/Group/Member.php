<?php

	class Logic_Space_Group_Member extends DbModel 
	{	
		/**
		 * 离开群组
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 */
		public static function leave($gid, $uid)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->delete('tb_group_member', 'uid = '.$uid.' AND gid = '.$gid);
				$db->delete('tb_msg', 'sender = '.$uid.' AND gid = '.$gid);
				$db->delete('tb_msg', 'incept = '.$uid.' AND gid = '.$gid);
				$db->commit();
				
			} catch (Exception $e){
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 更新成员群组角色
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @param unknown_type $role
		 */
		public static function crole($gid, $uid, $role, $time = false)
		{
			if($time == false) 
			$data = array('role' => $role);
			else $data = array('role' => $role, 'jointime' => $time);
			parent::Space()->update('tb_group_member', $data, 'gid = '.$gid.' AND uid = '.$uid);
		}
		
		/**
		 * 申请加入群
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 */
		public static function join($gid, $uid)
		{
			parent::Space()->insert('tb_group_member', array(
				'uid' => $uid,
				'gid' => $gid,
				'role' => 'join'
			));
		}
		
		/**
		 * 邀请加入群
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 */
		public static function invite($gid, $uid)
		{
			parent::Space()->insert('tb_group_member', array(
				'uid' => $uid,
				'gid' => $gid,
				'role' => 'invite'
			));
		}
		
		/**
		 * 获取用户所在群组的角色
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function role($gid, $uid)
		{
			$row = parent::Space()->fetchRow('SELECT `role` FROM `tb_group_member` 
												WHERE `gid` = ? AND `uid` = ?', 
			array($gid, $uid)); return $row['role'];
		}
		
		/**
		 * 是否为群组的创建者
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isCreater($gid, $uid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group_member` 
												WHERE `gid` = ? AND `uid` = ? AND `role` = "creater"', 
			array($gid, $uid));
		}
		
		/**
		 * 是否为群组成员
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isMemeber($gid, $uid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group_member` 
				WHERE `gid` = ? AND `uid` = ? AND (`role` = "member" OR `role` = "creater" OR `role` = "manager")', 
			array($gid, $uid));
		}
		
		/**
		 * 是否为群组管理员
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function isManager($gid, $uid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group_member` 
												WHERE `gid` = ? AND `uid` = ? AND `role` = "manager"', 
			array($gid, $uid));
		}
		
		/**
		 * 群组所有成员数量
		 *
		 */
		public static function num($gid)
		{
			$row = parent::Space()->fetchRow('SELECT COUNT(`uid`) AS `numrow` FROM `tb_group_member` 
				WHERE `gid` = ? AND (`role` = "member" OR `role` = "creater" OR `role` = "manager")', 
			$gid);
			return $row['numrow'];
		}
		
		/**
		 * 罗列群组名单
		 *
		 * @param unknown_type $gid
		 * @param unknown_type $role
		 */
		public static function all($gid, $role = 'member')
		{
			return parent::Space()->fetchAll('SELECT * FROM `tb_group_member` 
												WHERE `gid` = ? AND `role` = ?', 
			array($gid, $role));
		}

	}
	
?>