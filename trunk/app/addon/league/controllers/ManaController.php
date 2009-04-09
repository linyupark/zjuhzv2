<?php

	/**
	 * 大学生实习联盟管理控制器
	 *
	 */
	class Addon_League_ManaController extends Zend_Controller_Action 
	{
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
			$this->view->mode = $params['mode'];
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