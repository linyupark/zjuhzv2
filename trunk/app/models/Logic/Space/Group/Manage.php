<?php

	/**
	 * 群组管理
	 *
	 */
	class Logic_Space_Group_Manage extends DbModel 
	{
		/**
		 * 发送群信息
		 *
		 * @param unknown_type $params
		 */
		public static function msg($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try{	
				$incepters = $db->fetchAll('SELECT `uid` FROM `tb_group_member` WHERE `gid` = ?', $params['id']);
				foreach ($incepters as $i)
				{
					Logic_Space_Msg::group($i['uid'], $params['uid'], $params['id'], Alp_String::html($params['content']));
				}
				$db->commit();
				
			} catch (Exception $e){
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 基本信息
		 *
		 */
		public static function basic($params)
		{
			parent::Space()->update('tb_group', array(
				'type' => $params['type'],
				'name' => $params['name'],
				'intro' => Alp_String::html($params['intro']),
				'notice' => Alp_String::html($params['notice']),
			), 'gid = '.$params['id']);
		}
	}

?>