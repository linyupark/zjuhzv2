<?php

	/**
	 *  附件组件 - 投票系统
	 *
	 */
	class Addon_Console_VoteController extends Zend_Controller_Action 
	{
		/**
		 * 处理提交的投票表单
		 *
		 */
		function docreateAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$request = $this->getRequest();
			if($request->isXmlHttpRequest())
			{
				$params = Filter_Addon::createVote($request->getParams());
				if(Alp_Sys::getMsg() == null)
				{
					$vid = Logic_Addon_Vote::insert($params);
					if($vid != 0)
					{
						echo '<div class="success">提交成功, 系统将自动转向发起的投票</div>';
						echo Alp_Sys::jump('/addon_vote/?vid='.$vid, 2);
						exit();
					}
				}
				echo '<script>alert("'.Alp_Sys::allMsg('*','\n').'");</script>';
			}
		}
		
		function indexAction()
		{
			$this->view->headTitle('投票');
		}
	}

?>