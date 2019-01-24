<?php
namespace lib;

require_once root_path."vendor/autoload.php";

use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\Exceptions\BarcodeException;

class Barcode {

    static $error = 0;

    public static function generatorHTML($code = "")
    {
        self::$error = 0;
        $barcode = new BarcodeGeneratorHTML();

        try {
            return $barcode->getBarcode($code, $barcode::TYPE_EAN_13);
        } catch (BarcodeException $e) {
            return self::exception($e);
        }
    }

    public static function generatorJPG($code = "", $widthFactor = 2, $totalHeight = 30)
    {
        self::$error = 0;
        $barcode = new BarcodeGeneratorJPG();

        try {
            return $barcode->getBarcode($code, $barcode::TYPE_EAN_13, $widthFactor, $totalHeight);
        } catch (BarcodeException $e) {
            return self::exception($e);
        }
    }

    public static function generatorPNG($code = "",$widthFactor = 2, $totalHeight = 30)
    {
        self::$error = 0;
        $barcode = new BarcodeGeneratorPNG();

        try {
            return $barcode->getBarcode($code, $barcode::TYPE_EAN_13, $widthFactor, $totalHeight);
        } catch (BarcodeException $e) {
            return self::exception($e);
        }
    }

    public static function generatorSVG($code = "")
    {
        self::$error = 0;
        $barcode = new BarcodeGeneratorSVG();

        try {
            self::$error = 0;
            return $barcode->getBarcode($code, $barcode::TYPE_EAN_13);
        } catch (BarcodeException $e) {
            return self::exception($e);
        }
    }

    public static function exception($exception = BarcodeException::class)
    {
        self::$error = 1;
        $class = get_class($exception);
        if($class == 'Picqer\Barcode\Exceptions\InvalidCharacterException')
        {
            return "Barcode có chứa ký tự không hợp lệ";
        }
        elseif ($class == 'Picqer\Barcode\Exceptions\InvalidCheckDigitException')
        {
            return "Barcode không hợp lệ";
        }
        elseif ($class == 'Picqer\Barcode\Exceptions\InvalidFormatException')
        {
            return "Định dạng barcode không hợp lệ";
        }
        elseif ($class == 'Picqer\Barcode\Exceptions\InvalidLengthException')
        {
            return "Chiều dài barcode không hợp lệ";
        }
        elseif ($class == 'Picqer\Barcode\Exceptions\UnknownTypeException')
        {
            return "Lỗi không xác đinh khi tạo barcode";
        }
        else
        {
            return "Không thể tạo barcode";
        }
    }
}