<?php

	/**
	 * 文件共享帖数据操作
	 *
	 */
	class Logic_Space_Bar_Share extends DbModel
	{
		/**
		 * 更新某文件的下载次数
		 *
		 * @param unknown_type $id
		 */
		public static function download($id)
		{
			parent::Space()->update('tb_share', array('download'=>new Zend_Db_Expr('download+1')), 'id = '.(int)$id);
		}
		
		/**
		 * 删除指定的共享文件记录
		 *
		 * @param unknown_type $id
		 */
		public static function del($id)
		{
			parent::Space()->delete('tb_share', 'id = '.(int)$id);
		}
		
		/**
		 * 罗列所有跟tid有关的共享文件单元数据
		 *
		 * @param unknown_type $tid
		 * @return unknown
		 */
		public static function items($tid)
		{
			return parent::Space()->fetchAll('SELECT * FROM `tb_share` WHERE `tid` = ? ORDER BY id', $tid);
		}
		
		/**
		 * 查看详细
		 *
		 * @param unknown_type $tid
		 * @return unknown
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname','u.sex','sign'));
			return $select->query()->fetchAll();
		}
		
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
				
				foreach ($params['ids'] as $id)
				{
					$db->update('tb_share', array(
						'intro' => Alp_String::html($params['intros'][$id])
					), 'id ='.$id);
				}
				if(isset($params['n_files']))
				foreach($params['n_files'] as $k => $file)
				{
					$db->insert('tb_share', array(
						'tid' => $tid,
						'file' => $file,
						'intro' => Alp_String::html($params['n_intros'][$k])
					));
				}
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 建立新共享贴
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
				foreach($params['files'] as $k => $file)
				{
					$db->insert('tb_share', array(
						'tid' => $tid,
						'file' => $file,
						'intro' => $params['intros'][$k],
						'download' => 0
					));
				}
				
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