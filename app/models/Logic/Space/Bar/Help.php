<?php

	class Logic_Space_Bar_Help extends DbModel
	{
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