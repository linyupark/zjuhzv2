<?php

	/**
	 * 大学生实习联盟管理控制器
	 *
	 */
	class Addon_League_ManaController extends Zend_Controller_Action 
	{
		function init(){ if(Cmd::role() != 'master' && Cmd::role() != 'power' ) exit(); }
		
		function newsAction()
		{
			$request = $this->getRequest();
			$file = CFROOT.'league_news';
			if(!file_exists($file)) @touch($file);
			$this->view->news = file_get_contents($file);
			if($request->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				if($request->isPost())
				{
					$news = $request->getParam('content');
					@file_put_contents($file, stripslashes($news));
					echo 'success';
				}
				else
				{
					$content = $this->view->news;
					$arr = explode("\n", Cmd::b2h($content));
					echo '<ul style="padding:0;margin:0">';
					foreach($arr as $item){
						if(trim($item)) echo "<li>{$item}</li>";
					}
					echo '</ul>';
				}
			}
		}
		
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			switch ($params['mode'])
			{
				case 'update' : // 更新
					$corp_id = $params['id'];
					$data = DbModel::getSqlite('league.s3db')->fetchRow('
						SELECT * FROM `tb_corp` WHERE `corp_id` = ?', $corp_id);
					$this->view->data = $data;
				break;
				
				default : // 新增
					$params['mode'] = 'new';
				break;
			}
			$cf = new Zend_Config_Xml(CFROOT.'league.xml');
			$this->view->trade = $cf->trade->item->toArray();
			$this->view->func = $cf->func->item->toArray();
			$this->view->mode = $params['mode'];
		}
		
		/**
		 * 删除资料
		 *
		 */
		function delAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$uid = $this->_getParam('uid');
			DbModel::getSqlite('league.s3db')->delete('tb_resume', 'uid = '.(int)$uid);
			echo 'success';
		}
		
		
		/**
		 * 企业数据操作
		 *
		 */
		function docorpAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$params = $this->getRequest()->getParams();
			switch ($params['mode'])
			{
				case 'new' : // 增加
					$params = Filter_Addon::corp($params);
					if(Alp_Sys::getMsg() == null)
					{
						Logic_Addon_League::insert($params);
						echo 'success';
						return ;
					}
					echo '<div class="error">'.Alp_Sys::allMsg().'</div>';
				break;
				
				case 'update' : // 更新
					$params = Filter_Addon::corp($params);
					if(Alp_Sys::getMsg() == null)
					{
						Logic_Addon_League::update($params);
						echo 'success';
						return ;
					}
					echo '<div class="error">'.Alp_Sys::allMsg().'</div>';
				break;
				
				case 'delete' : // 删除
					Logic_Addon_League::del($params['id']);
				break;
			}
		}
	}

?>