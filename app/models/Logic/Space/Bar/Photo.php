<?php

	/**
	 * 看图论事帖数据操作
	 *
	 */
	class Logic_Space_Bar_Photo extends DbModel
	{
		public static function del($id)
		{
			parent::Space()->delete('tb_photo', 'id = '.$id);
		}
		
		/**
		 * 返回含有照片总数
		 *
		 * @param unknown_type $tid
		 * @return unknown
		 */
		public static function num($tid)
		{
			$row = parent::Space()->fetchRow('SELECT COUNT(`id`) AS `numrow` FROM `tb_photo` WHERE `tid` = ?', $tid);
			return $row['numrow'];
		}
		
		public static function mod($params, $tid)
		{
			$db = parent::Space();
			foreach ($params['ids'] as $id)
			{
				$db->update('tb_photo', array(
					'intro' => Alp_String::html($params['intros'][$id])
				), 'id ='.$id);
			}
			
			if(isset($params['n_photo']))
			foreach($params['n_photo'] as $k => $file)
			{
				$db->insert('tb_photo', array(
					'tid' => $tid,
					'file' => $file,
					'intro' => Alp_String::html($params['n_intros'][$k])
				));
			}
		}
		
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname'));
			return $select->query()->fetchAll();
		}
		
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
				foreach($params['photos'] as $k => $file)
				{
					$db->insert('tb_photo', array(
						'tid' => $tid,
						'file' => $file,
						'intro' => Alp_String::html($params['intros'][$k])
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