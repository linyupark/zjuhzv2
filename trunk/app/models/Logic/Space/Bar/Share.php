<?php

	/**
	 * 文件共享帖数据操作
	 *
	 */
	class Logic_Space_Bar_Share extends DbModel
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