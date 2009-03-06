<?php

	/**
	 * 用户个人资料设置
	 *
	 */
	class Space_Set_ProfileController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->headTitle('个人资料设置');
			$this->view->controller_name = 'profile';
		}
		
		function indexAction()
		{
			$this->_forward('base');
		}
		
		/**
		 * json 获取学院名单
		 *
		 */
		function getcampusAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$campus = Zend_Registry::get('config')->campus->name->toArray();
			echo Zend_Json::encode($campus);
		}
		
		/**
		 * 基础信息 
		 *
		 */
		function baseAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_User::base($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_User_Base::update($params, Cmd::uid());
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
					
				}
				echo Alp_Sys::allMsg('* ',"\n");
			}
			else 
			{
				$this->view->tab = 'base';
				$base = DbModel::User()->fetchRow('SELECT * FROM `tb_base` WHERE `uid` = ?', Cmd::uid());
				$this->view->base = $base;
			}
		}
		
		/**
		 * 教育信息
		 *
		 */
		function eduAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				if(isset($params['del'])) // 删除
				{
					Logic_User_Edu::delete($params['del'], Cmd::uid());
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				else // 添加
				{
					$params = Filter_User::edu($params);
					if(Alp_Sys::getMsg() == null)
					{
						Logic_User_Edu::insert($params, Cmd::uid());
						if(Alp_Sys::getMsg() == null)
						{
							echo 'success';
							exit();
						}
					}
					echo Alp_Sys::allMsg('* ',"\n");
				}
			}
			else 
			{
				$this->view->tab = 'edu';
				$edu = DbModel::User()->fetchAll('SELECT * FROM `tb_edu` WHERE `uid` = ?', Cmd::uid());
				$this->view->edu = $edu;
			}
		}
		
		/**
		 * 个人介绍
		 *
		 */
		function introAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_User::intro($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_User_Intro::update($params, Cmd::uid());
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ',"\n");
			}
			else 
			{
				$this->view->tab = 'intro';
				$intro = DbModel::User()->fetchRow('SELECT * FROM `tb_intro` WHERE `uid` = ?', Cmd::uid());
				// 插入uid
				if($intro == false)
				{
					DbModel::User()->insert('tb_intro', array('uid'=>Cmd::uid()));
				}
				$this->view->intro = $intro;
			}
		}
		
		/**
		 * 联系方式
		 *
		 */
		function contactAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_User::contact($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_User_Contact::update($params, Cmd::uid());
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ',"\n");
			}
			else 
			{
				$this->view->tab = 'contact';
				$contact = DbModel::User()->fetchRow('SELECT * FROM `tb_contact` WHERE `uid` = ?', Cmd::uid());
				$this->view->contact = $contact;
			}
		}
		
		/**
		 * 职业相关
		 *
		 */
		function careerAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				if(isset($params['del'])) // 删除
				{
					Logic_User_Career::delete($params['del'], Cmd::uid());
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				else // 添加
				{
					$params = Filter_User::career($params);
					if(Alp_Sys::getMsg() == null)
					{
						Logic_User_Career::insert($params, Cmd::uid());
						if(Alp_Sys::getMsg() == null)
						{
							echo 'success';
							exit();
						}
					}
					echo Alp_Sys::allMsg('* ',"\n");
				}
			}
			else 
			{
				$this->view->tab = 'career';
				$career = DbModel::User()->fetchAll('SELECT * 
					FROM `tb_career` WHERE `uid` = ? ORDER BY `start` ASC', Cmd::uid());
				$this->view->career = $career;
			}
		}
		
		/**
		 * 头像管理
		 *
		 */
		function headAction()
		{
			$this->view->tab = 'head';
		}
	}

?>