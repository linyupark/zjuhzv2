<?php

	/**
	 * 邮件控制器
	 *
	 */
	class Logic_Mail
	{	
		public static function address()
		{
			return DbModel::User()->fetchAll('
				SELECT `tb_contact`.`email` FROM `tb_contact` 
				LEFT JOIN `tb_base` ON `tb_base`.uid = `tb_contact`.uid 
				WHERE `tb_base`.role != "black"');
		}
		
		public static function batch($subject, $body, $mailaddess)
		{
			Zend_Loader::loadFile('Swift/swift_required.php');
			$transport = Swift_SmtpTransport::newInstance('zjuem.zju.edu.cn')
					   ->setUsername('zjuhz')
					   ->setPassword('18970521');
			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance($subject, $body, 'text/html')
					 ->setFrom(array('zjuhz@zju.edu.cn' => '杭州浙江大学校友会'))
					 ->setTo($mailaddess);
			
			if($mailer->batchSend($message, $faildaddress))
			return true; else return $faildaddress;
		}
		
		/**
		 * 返回邮箱设置
		 *
		 * @param unknown_type $set
		 * @return unknown
		 */
		public static function config($set = null)
		{
			if($set == null)
			return array(
				'name' => 'smtp.gmail.com', // p:465 , ssl
				'username' => 'service@zjuhz.com',
				'password' => '89988185-service_zjuhz_com',
				'auth' => 'login',
				'ssl' => 'ssl'
			);
			else return $set;
		}
	}

?>