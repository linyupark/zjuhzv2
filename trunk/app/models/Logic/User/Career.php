<?php
	
	/**
	 * 教育情况
	 *
	 */
	class Logic_User_Career extends DbModel
	{
		static function get($uid)
		{
			return parent::User()->fetchAll('SELECT * FROM `tb_career` WHERE `uid` = ?', $uid);
		}
		
		static function delete($id, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->delete('tb_career', "id={$id} AND uid={$uid}");
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
				if($params['end'] != 0)
				{
					$end = strtotime($params['end'][0].'-'.$params['end'][1]);
				}
				else $end = 0;
				$db->insert('tb_career', array(
					'uid' => $uid,
					'company' => $params['company'],
					'department' => $params['department'],
					'job' => $params['job'],
					'start' => strtotime($params['start'][0].'-'.$params['start'][1]),
					'end' => $end
				));
				$db->commit();
				
			} catch (Exception $e) {
				
				Alp_Sys::msg('exception', $e->getMessage());
				$db->rollback();
			}
		}
	}

?>