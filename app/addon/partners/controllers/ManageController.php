<?php

	class Addon_Partners_ManageController extends Zend_Controller_Action 
	{
		private $params;
		private $uid;
		
		function init()
		{
			// 身份审核
			$uid = Cmd::getSess('addon_partner');
			if($uid == null)
			$this->_redirect('/addon_partners/user');
			else $this->uid = $uid;
			
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 建立新企业
		 *
		 */
		function corpcreateAction()
		{
			$this->view->headTitle('添加新企业信息');
			$data = Cmd::getSess('addon_partner_setup');
			$step = $this->_getParam('step', 0);
			switch ($step)
			{
				case 0 : 
					$this->view->name = $data['name'];
				break;
				case 1 :
					$this->view->intro = htmlspecialchars(stripslashes($data['intro']));
				break;
				case 2 :
					$this->view->tel = $data['tel'];
					$this->view->address = $data['address'];
					$this->view->website = $data['website'];
				break;
				default : 
					if($data == null)
					$this->_redirect('/addon_partners/list');
					
					$data['uid'] = $this->uid;
					$cid = Logic_Addon_Partners::insertCorp($data);
					if($cid > 0)
					{
						$logo = UPLOADROOT.'addon/partners/logos/'.md5($this->uid).'.gif';
						if(file_exists($logo))
						{
							Alp_Image::init($logo)->output($cid, 'gif', null, null, true);
						}
						Cmd::setSess('addon_partner_setup', null);
					}
					else echo '<script>parent.alert(\''.Alp_Sys::allMsg('','\n').'\')</script>';
				break;
			}
			$this->view->step = $step;
			$this->view->uid = $this->uid;
		}
		
		function uploadAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$file_path = UPLOADROOT.'addon/partners/files/';
			Alp_Upload::init(array(
				'path' => $file_path,
				'type' => 'jpg|png|gif', 
				'maxsize' => 500,
				'random' => true
			));
			if(Alp_Upload::isUploaded('file'))
			{
				if(Alp_Upload::handle('file') == true)
				{
					echo '<script>parent.insert_html("<img src=\'/upload/addon/partners/files/'.Alp_Upload::$filename[0].'\' />")</script>';
				}
				else echo '<script>parent.alert(\''.Alp_Sys::allMsg('','\n').'\')</script>';
			}
		}
		
		/**
		 * 建立第三步
		 *
		 */
		function setup2Action()
		{
			$tel = Alp_Valid::of($this->params['tel'], 'tel', '电话', 'trim|required');
			$address = Alp_Valid::of($this->params['address'], 'address', '地址', 'trim|required');
			$website = Alp_Valid::of($this->params['website'], 'website', '网站', 'trim|valid_url');
			if(Alp_Sys::msg() == null)
			{
				Cmd::setSess('addon_partner_setup', array(
					'tel' => $tel,
					'address' => $address,
					'website' => $website
				));
				echo '<script>parent.step(3)</script>';
			}
			else
			echo '<script>parent.alert(\''.Alp_Sys::allMsg('','\n').'\')</script>';
		}
		
		/**
		 * 建立第二步
		 *
		 */
		function setup1Action()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$intro = Alp_Valid::of($this->params['intro'], 'intro', '企业介绍', 'trim|required');
			if(Alp_Sys::msg() == null)
			{
				Cmd::setSess('addon_partner_setup', array(
					'intro' => $intro
				));
				echo '<script>parent.step(2)</script>';
			}
			else
			echo '<script>parent.alert(\''.Alp_Sys::allMsg('','\n').'\')</script>';
		}
		
		/**
		 * 建立第一步
		 *
		 */
		function setup0Action()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$logo_path = UPLOADROOT.'addon/partners/logos/';
			Alp_Upload::init(array(
				'path' => $logo_path,
				'type' => 'jpg|png|gif', 
				'maxsize' => 500,
				'overwrite' => true,
				'filename' => array(md5($this->uid))
			));
			// 上传图片处理
			if(Alp_Upload::isUploaded('logo'))
			{
				if(Alp_Upload::handle('logo') == true)
				{
					Alp_Image::init($logo_path.md5($this->uid))->output(md5($this->uid), 'gif', null, null, true);
					Alp_Image::init($logo_path.md5($this->uid).'.gif')->resize(md5($this->uid), 120, 120);
					echo '<script>parent.change_logo("'.md5($this->uid).'.gif")</script>';
				}
			}
			
			$name = Alp_Valid::of($this->params['name'], 'name', '企业名称', 'trim|required');
			if(Alp_Sys::msg() == null)
			{
				Cmd::setSess('addon_partner_setup', array(
					'name' => $name
				));
				echo '<script>parent.step(1)</script>';
			}
			else
			echo '<script>parent.alert(\''.Alp_Sys::allMsg('','\n').'\')</script>';
		}
	}

?>