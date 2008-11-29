<?php
	
	/**
	 * 教育情况
	 *
	 */
	class Logic_User_Edu extends DbModel
	{
		static function get($uid)
		{
			return parent::User()->fetchAll('SELECT * FROM `tb_edu` WHERE `uid` = ?', $uid);
		}
		
		static function delete($id, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->delete('tb_edu', "id={$id} AND uid={$uid}");
				$db->commit();
				
			} catch (Exception $e) {
				
				Alp_Sys::msg('exception', $e->getMessage());
				$e->rollback();
			}
		}
		
		static function insert($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->insert('tb_edu', array(
					'uid' => $uid,
					'campus' => $params['campus'],
					'year' => $params['year'],
					'class' => $params['class']
				));
				$db->commit();
				
			} catch (Exception $e) {
				
				Alp_Sys::msg('exception', $e->getMessage());
				$db->rollback();
			}
		}
	}

?>