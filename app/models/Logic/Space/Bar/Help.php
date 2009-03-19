<?php

	class Logic_Space_Bar_Help extends DbModel
	{
		/**
		 * 转移求助分类
		 *
		 * @param unknown_type $sid
		 * @param unknown_type $tsid
		 */
		public static function mv($sid, $tsid)
		{
			$db = parent::Space();
			$sbar = $db->fetchAll('SELECT `tid` FROM `tb_help` WHERE `sort` = ?', $sid);
			$db->beginTransaction();
			try{
				foreach ($sbar as $b)
				{
					$db->update('tb_help', array('sort' => $tsid), 'tid = '.$b['tid']);
				}
				// 被转移的分类rate回归0,目标分类加rate
				$db->update('tb_help_sort', array('rate' => 0), 'sort = '.$sid);
				$db->update('tb_help_sort', array('rate' => new Zend_Db_Expr('rate + '.count($sbar))), 'sort = '.$tsid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 更新备注
		 *
		 */
		public static function memo($tid, $memo)
		{
			parent::Space()->update('tb_help', array('memo' => $memo), 'tid = '.$tid);
		}
		
		/**
		 * 切换求助帖状态
		 *
		 * @param unknown_type $tid
		 * @param unknown_type $state
		 */
		public static function state($tid, $state)
		{
			parent::Space()->update('tb_help', array('state' => $state), 'tid = '.$tid);
		}
		
		/**
		 * 查看求助信息
		 *
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('help' => 'tb_help'), 'bar.tid = help.tid');
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname','u.sex'));
			$select->joinLeft(array('s'=>'zjuhzv2_space.tb_help_sort'), 'help.sort = s.sort', 
							  array('sortname' => 's.name'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 获取分类
		 *
		 */
		public static function getSorts()
		{
			return parent::Space()->fetchAll('SELECT * FROM `tb_help_sort` ORDER BY `rate` DESC');
		}
		
		/**
		 * 创建新分类
		 *
		 * @param unknown_type $sort
		 */
		public static function createSort($sortname)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_help_sort', array(
					'name' => $sortname
				));
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', '创建失败，请确认没有重复创建，错误代码：'.$e->getMessage());
			}
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
				$db->insert('tb_help', array(
					'tid' => $tid,
					'content' => $params['content'],
					'sort' => $params['sort'],
					'state' => 0
				));
				$db->update('tb_help_sort', array(
					'rate' => new Zend_Db_Expr('rate + 1')
				), 'sort = '.$params['sort']);
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