<?php

	/**
	 * 群组数据逻辑层
	 *
	 */
	class Logic_Space_Group extends DbModel 
	{	
		/**
		 * 计算指定群组的热度
		 *
		 * @param unknown_type $gid
		 */
		public static function countpoint($gid)
		{
			$db = parent::Space();
			// 主题1->2,回帖10->1,成员数量1->1 + 成员热心度总和/成员数量
			$bars = $db->fetchAll('
				SELECT `tid` 
				FROM `tb_tbar` 
				WHERE `group` = ? AND `deny` = 0', $gid);
			$barnum = count($bars); // 主题数
			if($barnum > 0)
			{
				$temp = array();
				foreach ($bars as $v)
				{
					$temp[] =$v['tid'];
				}
				$in_tid = implode(',', $temp);
			}
			if($in_tid)
			{
				$reply = $db->fetchRow('
					SELECT COUNT(`id`) AS `numrows` 
					FROM `tb_comment` 
					WHERE `tid` IN ('.$in_tid.')
				');
				$replynum = $reply['numrows']; // 回复数
			}
			$uids = Logic_Space_Group_Member::ids($gid);
			$mnum = count($uids); // 成员数量
			$row = $db->fetchRow('
				SELECT SUM(`point`) AS `mtp` 
				FROM zjuhzv2_user.`tb_base` 
				WHERE `uid` IN ('.implode(',', $uids).') 
			');
			$mtp = $row['mtp']; // 成员热心度总和
			$point =  $barnum*2 + round($replynum/10) + $mnum + round($mtp/$mnum);
			$db->update('tb_group', array('point' => $point), 'gid = '.$gid);
			return $point;
		}
		
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
				   ->where('m.role IN ("creater","member","manager")');
				   
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