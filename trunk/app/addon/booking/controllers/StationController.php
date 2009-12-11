<?php

	/**
	 * 各领票点查看，生成excel表单
	 *
	 */
	class Addon_Booking_StationController extends Zend_Controller_Action 
	{
		private $params;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
			if(!$this->params['pid']){ $this->render('error'); exit(); }
			$this->view->stations = Logic_Addon_Booking::getStations($this->params['pid']);
			$this->view->party = Logic_Addon_Booking::getParty($this->params['pid']);
			$this->view->pid = $this->params['pid'];
			if($this->params['psw'] != $this->view->party['password'])
			$this->render('login');
			$this->view->psw = $this->params['psw'];
		}
		
		function xlsAction()
		{
			$this->getHelper('layout')->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
			$id = $this->params['id'];
			$members = unserialize($this->view->party['member']);
			if($members != false)
			{
				$list = array();
				foreach ($members as $uid => $v)
				{
					if($v['address'] == $id)
					{
						$list[$uid] = $v;
					}
				}
				$objPHPExcel = new PHPExcel();
	            $objPHPExcel->getProperties()->setCreator("zjuhz.com");
	            $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com");
	            $objPHPExcel->getProperties()->setTitle($this->view->party['title'].'活动人员名单');
	            $objPHPExcel->setActiveSheetIndex(0);
	            $objPHPExcel->getActiveSheet()->SetCellValue('A1', '姓名');
	            $objPHPExcel->getActiveSheet()->SetCellValue('B1', '订票数');
	            $objPHPExcel->getActiveSheet()->SetCellValue('C1', '基本信息');
	            $objPHPExcel->getActiveSheet()->SetCellValue('D1', '手机');
	            $objPHPExcel->getActiveSheet()->SetCellValue('E1', '签到');
	            $row = 2;
	            foreach($list as $m)
	            {
	                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $m['rname']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $m['tnum']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, Zend_Registry::get('config')->campus->name->$m['campus'].$m['class'].'('.$m['year'].'年)');
	                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $m['mobile']);
	                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, '');
	                $row++;
	            }
	            $objPHPExcel->getActiveSheet()->setTitle($this->view->party['title'].'活动人员名单');
	            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	            header("Pragma: public");
	            header("Expires: 0");
	            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	            header("Content-Type: application/force-download");
	            header("Content-Type: application/octet-stream");
	            header("Content-Type: application/download");
	            header("Content-Disposition: attachment;filename=booking.xls"); 
	            header("Content-Transfer-Encoding: binary ");
	            $objWriter->save('php://output');
			}
		}
		
		
		/**
		 * HTML表格
		 *
		 */
		function indexAction()
		{
			$pid = $this->params['pid'];
			$id = $this->_getParam('id', $this->view->stations[0]['id']);
			
			$members = unserialize($this->view->party['member']);
			$tickets = 0;
			if($members != false)
			{
				$list = array();
				foreach ($members as $uid => $v)
				{
					if($v['address'] == $id)
					{
						$list[$uid] = $v;
						$tickets += $v['tnum'];
					}
				}
				$this->view->list = $list;
                //var_dump($list);
				$this->view->tickets = $tickets;
			}
			$this->view->station = Logic_Addon_Booking::getStation($pid, $id);
			$this->view->id = $id;
		}
	}

?>