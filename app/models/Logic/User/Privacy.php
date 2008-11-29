<?php

	/**
	 * 用户隐私控制
	 *
	 */
	class Logic_User_Privacy extends DbModel 
	{
		/**
		 * 控制信息初始化为默认
		 *
		 */
		public static function init($uid)
		{
			$access = Zend_Registry::get('config')->access->toArray();
			$home = Zend_Registry::get('config')->home->toArray();
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->insert('tb_privacy', array(
					'uid' => $uid,
					'access' => serialize($access),
					'home' => serialize($home)
				));
				$db->commit();
				return array('access'=>$access, 'home'=>$home);
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
		
		/**
		 * 获取信息访问控制
		 *
		 */
		public static function getAccess($uid)
		{
			// 判断是否需要初始化访问控制信息
			$row = parent::User()->fetchRow('SELECT `access` FROM `tb_privacy` WHERE `uid` = ?', $uid);
			if($row == false)
			{
				$result = self::init($uid);
				$access = $result['access'];
			}
			else $access = unserialize($row['access']);
			return $access;
		}
		
		/**
		 * 设置用户自定义信息访问控制
		 *
		 */
		public static function setAccess($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->update('tb_privacy', array(
					'access' => serialize($params)
				), 'uid = '.$uid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 获取用户首页信息控制
		 *
		 */
		public static function getHome($uid)
		{
			// 判断是否需要初始化访问控制信息
			$row = parent::User()->fetchRow('SELECT `home` FROM `tb_privacy` WHERE `uid` = ?', $uid);
			if($row == false)
			{
				$result = self::init($uid);
				$home = $result['home'];
			}
			else $home = unserialize($row['home']);
			return $home;
		}
		
		/**
		 * 设置用户首页信息控制
		 *
		 */
		public static function setHome($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{
				$db->update('tb_privacy', array(
					'home' => serialize($params)
				), 'uid = '.$uid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
	}

?>