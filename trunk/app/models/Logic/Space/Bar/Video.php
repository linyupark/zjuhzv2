<?php

	/**
	 * 视频数据操作类
	 *
	 */
	class Logic_Space_Bar_Video extends DbModel
	{
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
				
				$db->update('tb_video', array(
					'content' => $params['content'],
					'modtime' => time()
				), 'tid = '.$tid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 查看视频贴
		 *
		 * @param unknown_type $tid
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('v' => 'tb_video'), 'bar.tid = v.tid');
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 新帖
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
					'puber' => $params['uid'],
					'pubtime' => time(),
					'replytime' => time(),
					'group' => $params['group'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				));
				$tid = $db->lastInsertId();
				$db->insert('tb_video', array(
					'tid' => $tid,
					'content' => $params['content'],
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