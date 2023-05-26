<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once ("plugin/PHPExcel/Classes/PHPExcel.php");

class Excel {
	public function __construct() {
		$this -> excel = new PHPExcel();
		$this -> excel -> getProperties() -> setCreator("Sansoftwares") -> setTitle("Immigration - Report") -> setSubject("Immigration - Report - Report File") -> setCategory("Report file");
		$this -> worksheet = $this -> excel -> getActiveSheet();
		$this -> sheetHeadingStyle = array( 'font' => array('bold'		=> true,
															'size'		=> 14,
															'wrap'		=> false,
															'color'		=> array('rgb' => '204D74')
													 ),
											'fill' => array('type'		=> PHPExcel_Style_Fill::FILL_SOLID,
															'startcolor'=> array('rgb' => 'DCEDFF')
													 ),
											'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
									);
		$this -> sheetSubHeadingStyle = array('font' =>  array( 'bold'	=> true,
																'size'	=> 11,
																'wrap'	=> false,
																'color'	=> array('rgb' => '204D74')
													 ),
											'fill' => array('type'		=> PHPExcel_Style_Fill::FILL_SOLID,
															'startcolor' => array('rgb' => 'DCEDFF')
													 ),
											'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
									);
		$this -> sheetSubSubHeadingStyle = array('font' =>  array( 'bold'	=> true,
																'size'	=> 11,
																'wrap'	=> false,
																'color'	=> array('rgb' => '000000')
													 ),
											'fill' => array('type'		=> PHPExcel_Style_Fill::FILL_SOLID,
															'startcolor' => array('rgb' => 'D5D8DC')
													 ),
											'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
									);
		$this -> headerStyle = array('font' => array('bold'		=> true,
													 'wrap'		=> false,
													 'color'	=> array('rgb' => '0F226C')
													 ),
									 'fill' => array('type'		=> PHPExcel_Style_Fill::FILL_SOLID,
													 'startcolor' => array('rgb' => '9ECDFF')
													 ),
									 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => 'DCEDFF')))
									);

		$this -> subHeadStyle = array('font' => array('bold' => true, 'wrap' => true, 'color' => array('rgb' => 'FFFFFF')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => '0091C8')));

		$this -> subHeaderStyle = array('font' => array('bold' => true, 'wrap' => true, 'color' => array('rgb' => '204D74')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'DCEDFF')));

		$this -> subFooterStyle = array('font' => array('bold' => true, 'wrap' => true, 'color' => array('rgb' => '545454')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'eeeeee')));
		
		$this -> textDataCellStyle = array('font' => array('bold' => false, 'wrap' => true, 'color' => array('rgb' => '545454')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'eeeeee')),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => 'DDDDDD'))));
		
		$this -> intergerDataCellStyle = array('font' => array('bold' => false, 'wrap' => true, 'color' => array('rgb' => '545454')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'eeeeee')),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => 'DDDDDD'))));
		
		$this -> numericDataCellStyle = array('font' => array('bold' => false, 'wrap' => true, 'color' => array('rgb' => '545454')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'eeeeee')),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => 'DDDDDD'))));

		$this -> footerIntergerDataCellStyle = array('font' => array('bold' => true, 'wrap' => true, 'color' => array('rgb' => '204D74')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'DCEDFF')),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
		
		$this -> footerNumericDataCellStyle = array('font' => array('bold' => true, 'wrap' => true, 'color' => array('rgb' => '204D74')), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'DCEDFF')),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
	}

	function generateKeyForExcel($val) {
		return strtoupper(str_replace('_', " ", $val));
	}

	public function generateExcel($reportName, $data=array(), $allowedHeadersArr=array(), $sheetHeading=array()) {
		$i=1;
		$headerRowNo	= 0;
		if(count($allowedHeadersArr) > 0) {
			foreach($sheetHeading as $hkey=>$hval) {
				$headerRowNo++;
				$this -> worksheet -> mergeCellsByColumnAndRow(0,  $headerRowNo, count($allowedHeadersArr), $headerRowNo);
				$this -> worksheet -> setCellValueByColumnAndRow(0, $headerRowNo, $hval['data']);
				if($hval['type'] == 'heading') {
					$this -> worksheet -> getStyleByColumnAndRow(0, $headerRowNo) ->  applyFromArray($this -> sheetHeadingStyle);
				}
				if($hval['type'] == 'subheading') {
					$this -> worksheet -> getStyleByColumnAndRow(0, $headerRowNo) ->  applyFromArray($this -> sheetSubHeadingStyle);
				}
			}
			$headerRowNo++;
			$this -> worksheet -> mergeCellsByColumnAndRow(0,  $headerRowNo, count($allowedHeadersArr), $headerRowNo);
			$this -> worksheet -> setCellValueByColumnAndRow(0, $headerRowNo, "");
			$this -> worksheet -> getStyleByColumnAndRow(0, $headerRowNo) ->  applyFromArray($this -> sheetHeadingStyle);
			
		}
		foreach ($data as $key => $row) {
			if(empty($allowedHeadersArr)) {
				$tmpArr = (array)$row;
				$tmpArr = array_change_key_case($tmpArr,CASE_UPPER);
				$allowedHeadersArr = array_keys((array)$tmpArr);
				$allowedHeadersArr = array_combine($allowedHeadersArr, $allowedHeadersArr);
			}
			$rowNo = array_search($key, array_keys((array)$data))+$headerRowNo;
			foreach ($row as $head => $column) {
				$columnKey = strtoupper($head);
				if(!array_key_exists(trim($columnKey), $allowedHeadersArr)){
					continue;
				}
				$columnNo = array_search($columnKey, array_keys((array)$allowedHeadersArr)) + 1;
				if ($key == 0) {
					$this -> worksheet -> setCellValueByColumnAndRow(0, $rowNo + 1, 'S.No');
					if(array_key_exists(trim($columnKey), $allowedHeadersArr)){
						$columnTitle = $allowedHeadersArr[$columnKey];
					}
					$this -> worksheet -> setCellValueByColumnAndRow($columnNo, $rowNo + 1, $columnTitle);
					$this -> worksheet -> getStyleByColumnAndRow(0, $rowNo + 1) -> applyFromArray($this -> headerStyle);
					$this -> worksheet -> getStyleByColumnAndRow($columnNo, $rowNo + 1) -> applyFromArray($this -> headerStyle);
				}
				$this -> worksheet -> setCellValueByColumnAndRow(0, $rowNo + 2, $i);
				$this -> worksheet -> setCellValueByColumnAndRow($columnNo, $rowNo + 2, $column);
				$this -> worksheet -> getColumnDimensionByColumn(0)->setAutoSize(true);
				$this -> worksheet -> getColumnDimensionByColumn($columnNo)->setAutoSize(true);
			}
			$i++;
		}

		$this->downloadExeclData($reportName);
	}

	public function generateExcelWithoutSrNo($reportName, $data=array(), $allowedHeadersArr=array()) {
		$i=1;
		
		foreach ($data as $key => $row) {
			if(empty($allowedHeadersArr)) {
				$tmpArr = (array)$row;
				$tmpArr = array_change_key_case($tmpArr,CASE_UPPER);
				$allowedHeadersArr = array_keys((array)$tmpArr);
				$allowedHeadersArr = array_combine($allowedHeadersArr, $allowedHeadersArr);
			}
			$rowNo = array_search($key, array_keys((array)$data));
			foreach ($row as $head => $column) {
				$columnKey = strtoupper($head);
				if(!array_key_exists(trim($columnKey), $allowedHeadersArr)){
					continue;
				}
				$columnNo = array_search($columnKey, array_keys((array)$allowedHeadersArr));
				if ($key == 0) {
					if(array_key_exists(trim($columnKey), $allowedHeadersArr)){
						$columnTitle = $allowedHeadersArr[$columnKey];
					}
					$this -> worksheet -> setCellValueByColumnAndRow($columnNo, $rowNo + 1, $columnTitle);
					$this -> worksheet -> getStyleByColumnAndRow(0, $rowNo + 1) -> applyFromArray($this -> headerStyle);
					$this -> worksheet -> getStyleByColumnAndRow($columnNo, $rowNo + 1) -> applyFromArray($this -> headerStyle);
				}
				$pattern = "/<b>(.*?)<\/b>/";
				preg_match($pattern, $column, $matches);
				if(count($matches) > 0 ) {
					$this -> worksheet -> setCellValueByColumnAndRow($columnNo, $rowNo + 2, $matches[1]);
					$this -> worksheet -> getStyleByColumnAndRow($columnNo, $rowNo + 2) -> applyFromArray($this -> subHeaderStyle);
				} else {
					$this -> worksheet -> setCellValueByColumnAndRow($columnNo, $rowNo + 2, $column);
				}
			}
			$i++;
		}

		$this->downloadExeclData($reportName);
	}
	
	public function downloadExeclData($excelName = '') {
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $excelName  . '-' . date('YmdHis') . '.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		// always modified
		header('Cache-Control: cache, must-revalidate');
		// HTTP/1.1
		header('Pragma: public');
		$writer = PHPExcel_IOFactory::createWriter($this -> excel, 'Excel5');
		ob_end_clean();
		ob_start();
		$writer -> save('php://output');
	}
}
