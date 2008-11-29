<?php
	
	/**
	 * 用户介绍信息操作
	 *
	 */
	class Logic_User_Intro extends DbModel
	{
		static function get($uid)
		{
			return parent::User()->fetchRow('SELECT * FROM `tb_intro` WHERE `uid` = ?', $uid);
		}
		
		static function update($params, $uid)
		{
			$db = parent::User();
			$db->beginTransaction();
			try 
			{	
				$db->update('tb_intro', array(
					'intro' => $params['intro'],
					'motto' => $params['motto'],
					'wish' => $params['wish'],
					'interest' => $params['interest'],
					'book' => $params['book'],
					'movie' => $params['movie'],
					'idol' => $params['idol'],
					'tv' => $params['tv']
				), 'uid = '.$uid);
				$db->commit();
				
				// 更新session基础数据
				Cmd::setSess('profile', array(
					'intro' => $params['intro'],
					'motto' => $params['motto'],
					'wish' => $params['wish'],
					'interest' => $params['interest'],
					'book' => $params['book'],
					'movie' => $params['movie'],
					'idol' => $params['idol'],
					'tv' => $params['tv']
				));
			} catch (Exception $e){
				Alp_Sys::msg('exception', $e->getMessage());
				$db->rollback();
			}
		}
	}

?>