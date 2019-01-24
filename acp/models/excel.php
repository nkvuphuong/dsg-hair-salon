<?php

namespace models;
include_once("/../tools/excelphp/PHPExcel.php");
use lib\input;
use lib\db;

class excel {
    static public $objPHPExcel;
    function __construct()
    {
    	global $CMS, $DB;

        // required lib

        
    	
    }

    static function build()
    {
        // Set document properties
        // print dirname(__FILE__)."../tools/excelphp/PHPExcel.php";exit;
        
        $PHPExcel = new PHPExcel();
        print "asdsa";
        var_dump(self::$objPHPExcel);
        self::$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("Office 2007 XLSX Test Document")
                             ->setSubject("Office 2007 XLSX Test Document")
                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Test result file");
    }


    
    
}  