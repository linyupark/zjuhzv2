<?php
	
	/**
	 * 用户密码
	 *
	 */
	class Logic_User_Password extends DbModel
	{
		public static function update($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{	
				$db->update('tb_base', array(
					'password' => md5($params['password'])
				), 'uid = '.$uid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
	}

?>