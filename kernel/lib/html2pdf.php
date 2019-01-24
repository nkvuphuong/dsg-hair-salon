<?php

$CMS->class->html2pdf = new class_html2pdf;
 
class class_html2pdf
{
	public $pdf;
 

	/**
	* BUILD EXCEL
	*/

	public function build()
	{
        $this->pdf = new \Mpdf\Mpdf();
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