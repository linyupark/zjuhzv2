<?php

	/**
	 * 密码相关开放控制
	 *
	 */
	class PasswordController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$this->view->headTitle('找回密码');
		}
		
		/**
		 * 发送重置密码的邮件
		 *
		 */
		function sendAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->_getAllParams();
				$username = Alp_Valid::of($params['username'], 'username', '真实姓名', 'trim|strip_tags|str_between[2,4]');
				$email = Alp_Valid::of($params['email'], 'email', '邮箱地址', 'trim|valid_email');
				$newpasswd = Alp_Valid::of($params['password'], 'password', '重设密码', 'trim|required');
				$code = Alp_Valid::of($params['code'], 'code', '校验码', 'trim|required');
				if(Alp_Sys::getMsg() == null)
				{
					$link = 'http://'.DOMAIN.'/public/password/reset/?code='.$code.
						'&np='.Alp_String::encrypy($newpasswd).
						'&ml='.Alp_String::encrypy($email);
					$html = file_get_contents(HTMLROOT.'/template/resetpassword.html');
					$html = str_replace('@uname@', $username, $html);
					$html = str_replace('@link@', $link, $html);
					$subject = 'zjuhz.com 系统邮件 - 重设密码';
					
					$set = Logic_Mail::config();
					$m = new Zend_Mail('UTF-8');
					$m->setFrom($set['username'], $subject);
					$m->addTo($email, $username);
					$m->setBodyHtml($html, 'UTF-8');
					$m->setSubject($subject);
					$m->send(new Zend_Mail_Transport_Smtp($set['name'], $set));
					echo 'success';
				}
				else echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		function checkoutAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$username = trim($this->_getParam('username'));
				$rows = DbModel::User()->fetchAll('SELECT `ct`.`email` FROM `tb_contact` AS `ct` 
					LEFT JOIN `tb_base` AS `base` ON `base`.`uid` = `ct`.`uid` 
					WHERE `base`.`username` = ?', $username);
				if(count($rows) > 0)
				{
					echo '<h3>找到以下符合邮箱地址：</h3>';
					foreach ($rows as $r)
					{
						echo '<p class="pdl10">'.$r['email'].'</p>';
					}
				}
				else echo '没有找到符合的邮箱地址';
			}
		}
	}

?>