<?php

	class Addon_Booking_IndexController extends Zend_Controller_Action 
	{
		private $params;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 倒计时
		 *
		 */
		function countorAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$time = $this->_getParam('t');
			$now = time();
			if($time > $now)
			{
				echo '离活动开始还有：'.Alp_Date::timespan($now, $time);
			}
		}
		
		/**
		 * 活动展示页
		 *
		 */
		function indexAction()
		{
			if(!$this->params['id'])
			{
				$this->render('error');
				exit();
			}
			$party = Logic_Addon_Booking::getParty($this->params['id']);
			$stations = Logic_Addon_Booking::getStations($this->params['id']);
			if(!$party){ $this->render('error'); }
			else
			{
				$this->view->headTitle($party['title']);
				$this->view->stations = $stations;
				$this->view->party = $party;
				$this->view->pid = $this->params['id'];
			}
            //var_dump($_SESSION);
		}
		
		/**
		 * 更新报名信息
		 *
		 */
		function updateAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$party = Logic_Addon_Booking::getParty($this->params['pid']);
				$member = unserialize($party['member']);
				if(!$member){
					$member = array();
					if($party['left'] < $this->params['tnum'])
					Alp_Sys::msg('error', '只剩下'.$party['left'].'张票，请减少订票数');
					$left = $party['left'] - $this->params['tnum'];
				}
				else 
				{
					$left = $member[$this->params['uid']]['tnum']+$party['left'];
					if($left < $this->params['tnum'])
					Alp_Sys::msg('error', '只剩下'.$left.'张票，请减少订票数');
					$left = $left - $this->params['tnum'];
				}

                // 热心度不到要求
                $degree_limit = (int)$party['degree'];
                $degree = Logic_Api::percentpoint(Cmd::getSess('profile','point'));
                if($degree_limit > $degree)
                {
                    Alp_Sys::msg('error', '您的热心度没有达到'.$degree_limit.'度，很遗憾您不能订票');
                }

                // 手机号码必须填写
                if((int)$this->params['mobile'] == 0)
                {
                    Alp_Sys::msg('error', '请前往<a href="/space_set/profile/contact">完善您的个人信息</a>（手机必填）');
                }

				$member[$this->params['uid']] = array('tnum'=>$this->params['tnum'],
														'address'=>$this->params['address'],
														'rname'=>$this->params['rname'],
														'year'=>$this->params['year'],
														'college'=>$this->params['college'],
														'major'=>$this->params['major'],
														'mobile'=>$this->params['mobile']);
					
				if(Alp_Sys::getMsg() == null)
				{
					$data = array('member'=>$member, 'left' => $left);
					Logic_Addon_Booking::update($data, $this->params['pid']);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ',"\n");
			}
		}
		
		/**
		 * 报名表单
		 *
		 */
		function bookingformAction()
		{
			$party = Logic_Addon_Booking::getParty($this->params['id']);
			$stations = Logic_Addon_Booking::getStations($this->params['id']);
			// 已经报名名单
			$members = unserialize($party['member']);
			if(isset($members[$this->params['uid']]))
			{
				// 更改订票数据
				$this->view->ticket = $members[$this->params['uid']]['tnum'];
				$this->view->address = $members[$this->params['uid']]['address'];
				$this->view->save = '保存修改';
			}
			//Zend_Debug::dump($this->params);
			$this->view->stations = $stations;
			$this->view->party = $party;
			$this->view->pid = $this->params['id'];
			$this->view->uid = $this->params['uid'];
            /*
			// v1版本基本信息
			$this->view->rname = $this->params['realName'];
			$this->view->year = $this->params['year'];
			$this->view->college = $this->params['college'];
			$this->view->major = $this->params['major'];
			$this->view->mobile = $this->params['mobile'];*/

            $user_profile = Cmd::getSess('profile');
            $user_edu = Logic_User_Edu::get($user_profile['uid']);
            $this->view->rname = $user_profile['username'];
			$this->view->year = $user_edu[0]['year'];
			$this->view->college = $user_edu[0]['campus'];
			$this->view->major = $user_edu[0]['class'];
			$this->view->mobile = $user_profile['mobile'];
            //var_dump($user_edu);
		}
		
		/**
		 * 获取用户信息v1
		 *
		 */
		function memberinfov1Action()
		{
			
		}
	}

?>