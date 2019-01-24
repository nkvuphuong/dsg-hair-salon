<?php

$CMS->class->excel = new class_excel;

class class_excel
{
	public $data;
	public $col = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

	/**
	* BUILD EXCEL
	*/

	public function build()
	{
		global $CMS;

		//require file
		require_once(root_path."acp/tools/excelphp/PHPExcel.php");
		
		// Create new PHPExcel object
		$this->data = new PHPExcel();

		// Set properties
		$this->data->getProperties()->setCreator("Lyhuuloi")
									 ->setLastModifiedBy("Lyhuuloi")
									 ->setTitle("Office 2007 XLSX")
									 ->setSubject("Office 2007 XLSX")
									 ->setDescription("CRM Report")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Report");	
									 
		// Wrapper
		$CMS->class->excel->data->getDefaultStyle()->getFont()->setName('Arial');
		$CMS->class->excel->data->getDefaultStyle()->getFont()->setSize(10);	
	}

	/**
	* SET SHEET
	*/

	public function set_sheet( $value = 0 )
	{
		global $CMS;
		
		$this->data->setActiveSheetIndex($value);
	}
	
	/**
	* SET VALUE
	*/
	
	public function set_value( $column = "", $value = "", $set_style = "", $set_cell = "" )
	{
		global $CMS;
		
		// Check for valid value
		if ( ! $column OR ! $value )
		{
			return false;	
		}
		
		// Set value with correlative column
		$this->data->getActiveSheet()->setCellValue($column, $value);
		
		// Set cell format
		if ( count($set_style) > 1 )
		{
			for ( $i = 0; $i < count($set_style); $i++ )
			{
				$this->set_style($column,$set_style[$i]);	
			}
		}
		else if ( $set_style )
		{
			$this->set_style($column,$set_style);
		}
		
		// Set cell format
		if ( $set_cell )
		{
			$this->set_cell($column,$set_cell);
		}
	}
	
	/**
	* SET COLUMN
	*/
	
	public function set_column( $value = "", $type = "auto" )
	{
		global $CMS;
		
		if ( strtolower($type) == "auto" )
		{
			$CMS->class->excel->data->getActiveSheet()->getColumnDimension($value)->setAutoSize(true);
		}
		else
		{
			$type = intval($type);
			$CMS->class->excel->data->getActiveSheet()->getColumnDimension($value)->setWidth($type);
		}
	}
	
	/**
	* SET TITLE
	*/
	
	public function set_title( $value = "" )
	{
		global $CMS;

		$this->data->getActiveSheet()->setTitle($value);
	}
	
	/**
	* SET STYLE
	*/
	
	public function set_style( $value = "", $style_name = "align_right" )
	{
		global $CMS;
		
		// Config style
		$style = array();
		$style['align_center'] = array( 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,), );
		$style['align_right'] = array( 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,), );
		$style['align_left'] = array( 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,), );

		$style['header'] = array(
			'font' => array('bold' => true,'size' => 11),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),
			'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),),
		);

		$style['bold'] = array( 'font' => array('bold' => true,'size' => 11) );
		$style['12'] = array( 'font' => array('size' => 12) );
		$style['13'] = array( 'font' => array('size' => 13) );
		$style['14'] = array( 'font' => array('size' => 14) );
		$style['15'] = array( 'font' => array('size' => 15) );
		
		$style['border'] = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN) ));
				
		// Wrap
		if ( $style_name == "wrap" )
		{
			$this->data->getActiveSheet()->getStyle($value)->getAlignment()->applyFromArray(array( 'wrap' => true ));	
		}
		// Color
		else if ( $style_name == "red" )
		{
			$FontColor = new PHPExcel_Style_Color();
			$FontColor->setRGB("ff0000");
			$this->data->getActiveSheet()->getStyle($value)->getFont()->setColor($FontColor);
		}
		// Normal
		else
		{
			$this->data->getActiveSheet()->getStyle($value)->applyFromArray($style[$style_name]);
		}
	}
	
	/**
	* MEGE CELL
	*/
	
	public function merge_cell( $value = "" )
	{
		global $CMS;
		
		$CMS->class->excel->data->getActiveSheet()->mergeCells($value);
	}
	
	/**
	* SET CELL
	*/

	public function set_cell( $value = "", $type = "currency" )
	{
		global $CMS;
		
		if ( substr($type,0,1) == ":" )
		{
			$CMS->class->excel->merge_cell($value.$type);
		}
		else if ( $type == "currency" )
		{
			$CMS->class->excel->data->getActiveSheet()->getStyle($value)->getNumberFormat()->setFormatCode('#,### đ');
		}
	}
	
	/**
	* DOWNLOAD
	*/

	public function download( $file_name = "Excel" )
	{
		global $CMS;

		// Redirect output to a client's web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$file_name.'.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($this->data, "Excel5");
		$objWriter->save('php://output');
		
		exit;
	}
}

?>