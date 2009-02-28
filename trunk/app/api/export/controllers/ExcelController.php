<?php

	/**
	 * Excel格式数据输出控制器
	 *
	 */
	class Api_Export_ExcelController extends Zend_Controller_Action 
	{
		function init()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 活动名单
		 *
		 */
		function eventsAction()
		{
			$members = Logic_Space_Bar_Events::members($this->_getParam('tid'));
			$objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("zjuhz.com");
            $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com");
            $objPHPExcel->getProperties()->setTitle('活动参加人员联系方式');
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', '姓名');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', '性别');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', '学院');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', '邮箱');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', '手机');
            $row = 2;
            foreach($members as $uid => $uname)
            {
            	$user = Logic_Api::user($uid);
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $uname);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $user[0]['sex']);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $user[0]['campus']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $user[0]['email']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $user[0]['mobile']);
                $row++;
            }
            $objPHPExcel->getActiveSheet()->setTitle('活动参加人员联系方式');
           $this->stream($objPHPExcel, 'contact');
		}
		
		function stream($objPHPExcel, $name)
		{
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename={$name}.xls"); 
            header("Content-Transfer-Encoding: binary ");
            $objWriter->save('php://output');
		}
	}
	
?>