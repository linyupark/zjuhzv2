<?php
	
	/**
	 * 用户基础信息操作
	 *
	 */
	class Logic_User_Base extends DbModel
	{
		/**
		 * 根据用户名获取其id
		 *
		 * @param unknown_type $uname
		 */
		static function uid($uname)
		{
			$db = parent::User();
			if(is_array($uname))
			{
				$select = $db->select();
				$select->from('tb_base', 'uid');
				foreach ($uname as $u) $select->orWhere('username = ?', $u);
				return $select->query()->fetchAll();
			}
			else return $db->fecthAll('SELECT `uid` FROM `tb_base` WHERE `username` = ?', $uname);
		}
		
		/**
		 * 检查用户id有效性
		 *
		 * @param unknown_type $uid
		 */
		static function check($uid)
		{
			return parent::User()->fetchRow('SELECT `uid` FROM `tb_base` WHERE `uid` = ?', $uid);
		}
		
		static function get($uid)
		{
			return parent::User()->fetchRow('SELECT * FROM `tb_base` WHERE `uid` = ?', $uid);
		}
		
		static function update($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->update('tb_base', array(
					'username' => $params['username'],
					'nickname' => $params['nickname'],
					'sex' => $params['sex'],
					'birthyear' => $params['year'],
					'birthmonth' => $params['month'],
					'birthday' => $params['day'],
					'hometown' => $params['hometown'],
					'city' => $params['city'],
					'marriage' => $params['marriage']
				), 'uid = '.$uid);
				$db->commit();
				
				// 更新session基础数据
				Cmd::setSess('profile', array(
					'username' => $params['username'],
					'nickname' => $params['nickname'],
					'sex' => $params['sex'],
					'birthyear' => $params['year'],
					'birthmonth' => $params['month'],
					'birthday' => $params['day'],
					'hometown' => $params['hometown'],
					'city' => $params['city'],
					'marriage' => $params['marriage']
				));
			} catch (Exception $e){
				Alp_Sys::msg('exception', $e->getMessage());
				$db->rollback();
			}
		}
	}

?>