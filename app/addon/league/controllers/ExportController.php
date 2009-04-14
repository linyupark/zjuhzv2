<?php

	class Addon_League_ExportController extends Zend_Controller_Action 
	{
		function init()
		{
			if(Cmd::role() != 'master' && Cmd::role() != 'power') exit();
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 导出 xls
		 *
		 */
		function xlsAction()
		{
			$ser = $this->_getParam('ser');
			if($ser) $where = ' WHERE `uid` IN ('.$ser.')';
			$rows = DbModel::getSqlite('league.s3db')->fetchAll('SELECT * FROM `tb_resume`'.$where);
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("zjuhz.com大学生实习联盟");
	        $objPHPExcel->getProperties()->setLastModifiedBy("zjuhz.com大学生实习联盟");
	        $objPHPExcel->getProperties()->setTitle('实习生资料表格');
	        $objPHPExcel->setActiveSheetIndex(0);
	        $objPHPExcel->getActiveSheet()->SetCellValue('A1', '姓名');
	        $objPHPExcel->getActiveSheet()->SetCellValue('A1', '性别');
	        $objPHPExcel->getActiveSheet()->SetCellValue('C1', '专业');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D1', '年级');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E1', '手机');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F1', '电话');
	        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Email');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H1', '住址');
	        $objPHPExcel->getActiveSheet()->SetCellValue('I1', '教育背景');
	        $objPHPExcel->getActiveSheet()->SetCellValue('J1', '培训经历');
	        $objPHPExcel->getActiveSheet()->SetCellValue('K1', '实习(项目)经历');
	        $objPHPExcel->getActiveSheet()->SetCellValue('L1', '获奖情况');
	        $objPHPExcel->getActiveSheet()->SetCellValue('M1', '个人描述');
	        $objPHPExcel->getActiveSheet()->SetCellValue('N1', '你希望自己毕业后从事哪类工作？为了这一目标，你曾做过哪些准备，未来还会怎样努力以达成目标？');
	        $objPHPExcel->getActiveSheet()->SetCellValue('O1', '你希望通过参与本次活动，取得哪些收获？');
	        $row = 2;
			foreach ($rows as $v)
			{
				$data = Logic_Api::user($v['uid']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row, $data[0]['username']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $data[0]['sex']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $v['major']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row, $v['grade']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $data[0]['mobile']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$row, $data[0]['tel']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $data[0]['email']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $data[0]['address']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $v['edu']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$row, $v['train']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$row, $v['internship']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$row, $v['award']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$row, $v['intro']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$row, $v['memo_1']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('O'.$row, $v['memo_2']);
	            $row++;
			}
			$objPHPExcel->getActiveSheet()->setTitle('实习生资料表格');
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename=league_tb.xls"); 
            header("Content-Transfer-Encoding: binary ");
            $objWriter->save('php://output');
		}
	}

?>