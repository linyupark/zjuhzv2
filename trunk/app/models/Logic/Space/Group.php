<?php

	/**
	 * 群组数据逻辑层
	 *
	 */
	class Logic_Space_Group extends DbModel 
	{	
		/**
		 * 我的群id
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function ids($uid)
		{
			return parent::Space()->fetchAll('SELECT `gid` 
				FROM `tb_group_member` 
				WHERE `uid` = '.$uid.' 
				AND `role` IN ("creater","member","manager")');
		}
		
		/**
		 * 我的群
		 *
		 */
		public static function my($uid)
		{
			$select = DbModel::Space()->select();
			$select->from(array('m' => 'tb_group_member'))
				   ->where('m.uid = ?', $uid)
				   ->where('m.role = "creater" OR m.role = "member" OR m.role = "manager"');
				   
			$select->joinLeft(array('g' => 'tb_group'), 'g.gid = m.gid');
			$select->order('m.role ASC');
			return  $select->query()->fetchAll();
		}
		
		/**
		 * 新群信息
		 *
		 * @param unknown_type $num
		 * @return unknown
		 */
		public static function fresh($num)
		{
			return parent::Space()->fetchAll('
				SELECT * FROM `tb_group` ORDER BY `createtime` DESC LIMIT '.$num);
		}
		
		/**
		 * 更新最后访问时间
		 *
		 */
		public static function visit($gid, $uid)
		{
			parent::Space()->update('tb_group_member', 
				array('lastvisit' => time()), "gid = {$gid} AND uid = {$uid}");
		}
		
		/**
		 * 是否有群组访问权限
		 *
		 */
		public static function isAllowedVisit($gid, $uid)
		{
			$group = self::info($gid);
			if(!$group) return false;
			if($group['type'] == 'close')
			return Logic_Space_Group_Member::isMemeber($gid, $uid);
			
			return true;
		}
		
		/**
		 * 获取群组信息
		 *
		 * @param unknown_type $gid
		 * @return unknown
		 */
		public static function info($gid)
		{
			return parent::Space()->fetchRow('SELECT * FROM `tb_group` WHERE `gid` = ?', $gid);
		}
		
		/**
		 * 建立新群组
		 *
		 */
		public static function create($params, $creater)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_group', array(
					'name' => $params['name'],
					'createtime' => time(),
					'intro' => $params['intro'],
					'type' => $params['type']
				));
				$gid = $db->lastInsertId();
				$db->insert('tb_group_member', array(
					'uid' => $creater,
					'gid' => $gid,
					'role' => 'creater',
					'jointime' => time()
				));
				$db->commit();
				return $gid;
				
			} catch (Exception $e){
				
				$db->rollback();
				Alp_Sys::msg('exception', '创建异常，可能该群组名已被使用，错误代码：'.$e->getMessage());
			}
			return false;
		}
	}
	
?>