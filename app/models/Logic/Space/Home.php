<?php

	/**
	 * 用户主页数据信息控制类
	 *
	 */
	class Logic_Space_Home extends DbModel 
	{
		/**
		 * 个人主页数据初始化
		 *
		 * @param unknown_type $uid
		 */
		public static function init($uid)
		{
			$db = parent::Space();
			$row = $db->fetchRow('SELECT `uid` FROM `tb_home` WHERE `uid` = ?', $uid);
			if(!$row) $db->insert('tb_home', array('uid' => $uid));
		}
		
		/**
		 * 获取个人主页指定数据
		 *
		 * @param unknown_type $col
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function get($col = '*', $uid)
		{
			return parent::Space()->fetchRow('SELECT `'.$col.'` FROM `tb_home` WHERE `uid` = ?', $uid);
		}
		
		public static function ing($v, $uid)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try
			{
				$db->update('tb_home', array('ing' => $v), 'uid = '.$uid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 获取指定要显示在首页的信息
		 *
		 * @param unknown_type $col
		 * @param unknown_type $uid
		 */
		public static function info($col, $uid)
		{
			$base = Logic_User_Base::get($uid);
			$intro = Logic_User_Intro::get($uid);
			$contact = Logic_User_Contact::get($uid);
			$edu = Logic_User_Edu::get($uid);
			$career = Logic_User_Career::get($uid);
			$trans_intro = Zend_Registry::get('config')->user_intro->toArray();
			$trans_contact = Zend_Registry::get('config')->user_contact->toArray();
			switch ($col)
			{
				case 'nickname' : 
					if(!$base['nickname']) return false;
					return '<tr><td class="quiet txtr">昵称：</td><td>'.$base['nickname'].'</td></tr>';
				break;
				case 'sex' : 
					return '<tr><td class="quiet txtr" width="17%">性别：</td><td>'.$base['sex'].'</td></tr>';
				break;
				case 'birth' : 
					if(!$base['birthyear'] || !$base['birthmonth'] || !$base['birthday']) return false;
					return '<tr><td class="quiet txtr">出生日期：</td><td>'."{$base['birthyear']} - 
														{$base['birthmonth']} - 
														{$base['birthday']}".'</td></tr>';
				break;
				case 'hometown' : 
					if(!$base['hometown']) return false;
					return '<tr><td class="quiet txtr">家乡：</td><td>'.$base['hometown'].'</td></tr>';
				break;
				case 'city' : 
					if(!$base['city']) return false;
					return '<tr><td class="quiet txtr">现居住地：</td><td>'.$base['city'].'</td></tr>';
				break;
				case 'marriage' : 
					if(!$base['marriage']) return false;
					return '<tr><td class="quiet txtr">婚姻：</td><td>'.$base['marriage'].'</td></tr>';
				break;
				case 'edu' : 
					$str = '';
					if(count($edu) > 0 && $edu != false)
					foreach($edu as $v)
					{
						$str .= '<tr><td class="quiet txtr">教育情况：</td><td>'.$v['campus'].'('.$v['class'].')'.$v['year'].'入学</td></tr>';
					}
					return $str;
				break;
				case 'intro' : 
					$str = '';
					if(count($intro) > 0 && $intro != false)
					foreach($intro as $k => $v)
					{
						if($intro[$k] && $trans_intro[$k])
						{
							$str .= '<tr class="intro hide"><td class="quiet txtr">'.$trans_intro[$k].'：</td><td>'.stripslashes($v).'</td></tr>';
						}
					}
					return $str;
				break;
				case 'contact' : 
					$str = '';
					if(count($contact) > 0 && $contact != false)
					foreach($contact as $k => $v)
					{
						if($contact[$k] && $trans_contact[$k])
						{
							$str .= '<tr class="contact hide"><td class="quiet txtr">'.$trans_contact[$k].'：</td><td>'.stripslashes($v).'</td></tr>';
						}
					}
					return $str;
				break;
				case 'career' : 
					$str = '';
					if(count($career) > 0 && $career != false)
					foreach($career as $i => $v)
					{
						$start = date('y/m', $v['start']);
						$end = ($v['end']==0)?'至今':date('y/m', $v['end']);
						$str .= '<tr class="career hide"><td class="quiet txtr">'.($i+1).'.</td>
									<td><span class="quiet">'.$start.'-'.$end.'</span> '.$v['company'].'('.$v['department'].')'.$v['job'].'</td></tr>';
					}
					return $str;
				break;
				default: 
					return null;	
				break;
			}
		}
	}

?>