<?php
	
	/**
	 * 用户联系方式数据操作
	 *
	 */
	class Logic_User_Contact extends DbModel
	{
		static function get($uid)
		{
			return parent::User()->fetchRow('SELECT * FROM `tb_contact` WHERE `uid` = ?', $uid);
		}
		
		static function update($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->update('tb_contact', array(
					'email' => $params['email'],
					'qq' => $params['qq'],
					'msn' => $params['msn'],
					'address' => $params['address'],
					'zipcode' => $params['zipcode'],
					'tel' => $params['tel'],
					'mobile' => $params['mobile']
				), 'uid = '.$uid);
				$db->commit();
				
				// 更新session基础数据
				Cmd::setSess('profile', array(
					'email' => $params['email'],
					'qq' => $params['qq'],
					'msn' => $params['msn'],
					'address' => $params['address'],
					'zipcode' => $params['zipcode'],
					'tel' => $params['tel'],
					'mobile' => $params['mobile']
				));
			} catch (Exception $e){
				Alp_Sys::msg('exception', $e->getMessage());
				$db->rollback();
			}
		}
	}

?>