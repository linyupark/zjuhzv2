<?php

	class Addon_Vote_IndexController extends Zend_Controller_Action 
	{
		/**
		 * 页面获取的参数集合
		 *
		 * @var array
		 */
		private $params = array();
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		function indexAction()
		{
			$vid = (int)$this->params['vid'];
			if($vid > 0)
			{
				$base = Logic_Addon_Vote::base($vid);
				if($base)
				{
					if($this->getRequest()->isXmlHttpRequest())
					{
						$this->getHelper('viewRenderer')->setNoRender();
						$params = Filter_Addon::vote($this->params);
						if(Alp_Sys::getMsg() == null)
						{
							if(Logic_Addon_Vote::doRate($params) == true)
							{
								echo '<div class="success">投票成功！系统即将刷新投票页</div>';
								echo Alp_Sys::jump('/addon_vote/?vid='.$vid, 2);
								exit();
							}
						}
						echo '<script>alert("'.Alp_Sys::allMsg('*','\n').'");</script>';
					}
					else 
					{
						$selects = $_COOKIE['zjuhz_addon_vote_'.$vid];
						if($selects == null)
						{
							$this->view->limit = $base['mulit'];
						}
						else 
						{
							$this->view->limit = 0;
							$this->view->selected = unserialize($selects);
						}
						$this->view->base = $base;
						$this->view->options = Logic_Addon_Vote::options($vid);
						$this->view->total_rate = Logic_Addon_Vote::totalRate($vid);
					}
				} 
				else echo Alp_Sys::jump('/addon_vote/?vid=0');
			}
			else $this->render('error');
		}
	}

?>