<?php

	/**
	 * 话题贴数据操作
	 *
	 */
	class Logic_Space_Bar_Topic extends DbModel 
	{	
		/**
		 * 查看话题贴
		 *
		 * @param unknown_type $tid
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('topic' => 'tb_topic'), 'bar.tid = topic.tid');
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 新话题贴
		 *
		 * @param unknown_type $params
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
					'group' => $params['group'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				));
				$tid = $db->lastInsertId();
				$db->insert('tb_topic', array(
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