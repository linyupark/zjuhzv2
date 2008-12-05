<?php

	/**
	 * 看图论事帖数据操作
	 *
	 */
	class Logic_Space_Bar_Photo extends DbModel
	{
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
				foreach($params['photos'] as $k => $file)
				{
					$db->insert('tb_photo', array(
						'tid' => $tid,
						'file' => $file,
						'intro' => $params['intros'][$k]
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