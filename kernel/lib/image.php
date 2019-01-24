<?php
namespace lib;
use core\ezy;
use Symfony\Component\Config\Definition\Exception\Exception;

$CMS->class->image = new image;

class image
{
	public $quality = 1; // Set = 1 if u want to fixed thumb by cut the real image
	
    public $phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );

	public function resize($img, $newfilename, $fixed_width = 120, $fixed_height = 0)
	{
		global $CMS;

		$thumb_width = $fixed_width;
		$max_width = $thumb_width;
	
		if (!extension_loaded('gd') && !extension_loaded('gd2'))
		{
			trigger_error("GD is not loaded", E_USER_WARNING);
			return false;
		}
	
		list($width_orig, $height_orig, $image_type) = getimagesize($img);
	   
		switch ($image_type)
		{
			case 1:$im = imagecreatefromgif($img); break;
			case 2: $im = imagecreatefromjpeg($img);  break;
			case 3: $im = imagecreatefrompng($img); break;
			default:  trigger_error('File type unsupported', E_USER_WARNING);  break;
		}
	   
		$aspect_ratio = (float) $height_orig / $width_orig;
		
		$thumb_height = round($thumb_width * $aspect_ratio);

		while( $thumb_height > $max_width )
		{
			$thumb_width-=10;
			$thumb_height = round($thumb_width * $aspect_ratio);
		}
		
		// Check for fixed height
		if ( $fixed_height > 0 )
		{
			$thumb_width = $fixed_width;
			$thumb_height = $fixed_height;	
		}
		
		$newImg = imagecreatetruecolor($thumb_width, $thumb_height);
	   
		// Transparent for GIF or PNG
		if(($image_type == 1) OR ($image_type==3))
		{
			imagealphablending($newImg, false);
			imagesavealpha($newImg,true);
			$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
		}

		// Quality good
		if ( $this->quality == 1 && $fixed_height )
		{
			if ( $width_orig > $height_orig )
			{
				$width_orig = $height_orig;
			}
			else
			{
				$height_orig = $width_orig;
			}
		}
		
		imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);
	   
		// Create file
		switch ($image_type)
		{
			case 1: imagegif($newImg,$newfilename); break;
			case 2: @imagejpeg($newImg,$newfilename);  break;
			case 3: imagepng($newImg,$newfilename); break;
			default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
		}

		return $newfilename;
	}

    /**
     * Watermask v1.0 LHL-2010-03-22, Raw watermask
     * Watermask V2.0 LHL-2018-08-10, Updated background + position
     * @param $watermask_sources
     * @param $sources
     * @param $target
     * @return bool
     */

    public function watermask( $watermask_sources, $sources, $target, $background = "#FFFFFF", $watermask_adjust = [] )
    {
        global $CMS;

        // if (!file_exists($watermask_sources)) {
        // return false;
        // }

        // Init image
        $image = $this->init($sources);

        // Return width, height
        $imagewidth = imagesx($image);
        $imageheight = imagesy($image);

        // LHL-2018-08-10: Fix png transparent, very important!
        imagealphablending($image, true); // False = Marked water mask no transparent
        imagesavealpha($image, true); // True = Keep transparent in T-shirt

        // Background
        $background = $background == "#000000" ? "#000001" : $background;
        $colorCode = $this->convert_hex2rgb($background);
        $colorRgb = array('red' => $colorCode['red'], 'green' => $colorCode['green'], 'blue' => $colorCode['blue']);

        $frameImage = imagecreatetruecolor($imagewidth, $imageheight);
        if(!$frameImage) { print "Error function PHP: imagecreatetruecolor();<br/>"; }

        $color = imagecolorallocate($frameImage, $colorRgb['red'], $colorRgb['green'], $colorRgb['blue']);
        if(!$color) { print "Error function PHP: imagecolorallocate();<br/>"; }

        imagefill($frameImage, 0, 0, $color);
        imagecopy($frameImage, $image, 0, 0, 0, 0, $imagewidth, $imageheight);

        // Water mask
        if(file_exists($watermask_sources))
        {
            $watermask = imagecreatefrompng($watermask_sources);

            if (!$watermask) {
                print "Error function PHP: imagecreatefrompng();<br/>";
            }

            // Adjust (Custom) watermask size
            if (count($watermask_adjust) > 0) {
                // Ensure input have 8 items
                if (count($watermask_adjust) < 8) {
                    exit("Example input (Unit: Percent): [\"frame_top\" => 22,
                            \"frame_left\" => 28,
                            \"frame_width\" => 45,
                            \"frame_height\" => 42,
                            \"design_top\" => 0,
                            \"design_left\" => 0,
                            \"design_width\" => 100,
                            \"design_height\" => 74]");
                }

                // Assign to $size
                $size = $watermask_adjust;

                // Start watermask from x, y
                $startwidth = $size['frame_left'] / 100 * $imagewidth;
                $startheight = $size['frame_top'] / 100 * $imagewidth;

                // Add pixel for design left, top
                $startwidth += $size['design_left'] / 100 * $imagewidth;
                $startheight += $size['design_top'] / 100 * $imagewidth;

                // New size of design
                $framewidth = $size['frame_width'] / 100 * $imagewidth;
                $frameheight = $size['frame_height'] / 100 * $imagewidth;

                // Scale design (watermask)
                $designwidth = $size['design_width'] / 100 * $framewidth;
                $designheight = $size['design_height'] / 100 * $frameheight;
                $watermask = $this->imageResize($watermask, $designwidth, $designheight); // LHL-2018-08-22 don't use imagescale(), bicubic problem.
                //$watermask = imagescale($watermask, $designwidth, $designheight);

                // Get watermask size
                $watermaskwidth = imagesx($watermask);
                $watermaskheight = imagesy($watermask);

                imagecopy($frameImage, $watermask, $startwidth, $startheight, 0, 0, $watermaskwidth, $watermaskheight);
            } // Default watermask size
            else {
                $watermaskwidth = imagesx($watermask);
                $watermaskheight = imagesy($watermask);

                $startwidth = (($imagewidth - $watermaskwidth - 10));
                $startheight = (($imageheight - $watermaskheight - 10));

                imagecopy($frameImage, $watermask, $startwidth, $startheight, 0, 0, $watermaskwidth, $watermaskheight);
            }
        }

        //imagejpeg($frameImage,NULL,100); // Best quality is 80
        imagepng($frameImage, null, 9);

        imagedestroy($frameImage);
        imagedestroy($image);
        imagedestroy($watermask);

        $data = ob_get_contents();
        ob_end_clean();

        return file_put_contents($target, $data);
    }

    /**
     * LHL-2018-08-10: Convert #xxxXxx (Hex) to RGB
     * @param $hexStr
     * @param bool $return_string
     * @return array|bool|string
     */

    function convert_hex2rgb($hex, $return_string = false)
    {
        // Ensure the pasted string is hex
        $hex = preg_replace("/[^0-9A-Fa-f]/", "", $hex);

        $data = [];

        // Hex format like: fff000
        if (strlen($hex) == 6)
        {
            $color = hexdec($hex);
            $data['red'] = 0xFF & ($color >> 0x10);
            $data['green'] = 0xFF & ($color >> 0x8);
            $data['blue'] = 0xFF & $color;
        }
        // Hex format like: fff
        else if (strlen($hex) == 3)
        {
            $data['red'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $data['green'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $data['blue'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        }
        // Invalid hex
        else {
            return false;
        }

        return $return_string ? implode(",", $data) : $data;
    }

    /**
     * Image resize, alternative for imagescale
     * @param $src
     * @param $width
     * @param $height
     * @return mixed
     */

    public function imageResize($src, $new_width, $new_height)
    {
        $width = imagesx($src);
        $height = imagesy($src);

        $newimg = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($src, true); // False = Marked water mask no transparent
        imagesavealpha($src, true); // True = Keep transparent in T-shirt

        // Create transparent color
        $trans_colour = imagecolorallocatealpha($newimg, 0, 0, 0, 127);
        imagefill($newimg, 0, 0, $trans_colour);

        imagecopyresampled($newimg, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($src);

        //header("Content-Type: image/png");
        //imagepng($newimg);
        //exit;

        return $newimg;
    }
	
	/*
	 *	Create image
	 */
	
	public function init( $sources, $obstart = true )
	{
		if ( $obstart == true ){
		    ob_start();
        }
		
		$imagesource = $sources;
		
		$filetype = substr($imagesource,strlen($imagesource)-4,4);
		$filetype = strtolower($filetype);

		if($filetype == ".gif")  $image = @imagecreatefromgif($imagesource);  
		if($filetype == ".jpg")  $image = @imagecreatefromjpeg($imagesource);  
		if($filetype == ".png")  $image = @imagecreatefrompng($imagesource);  
		if (!$image) die();
		
		return $image;
	}
	
	public function create($image)
	{
		imagejpeg($image);
		imagedestroy($image);
	
		$data = ob_get_contents();
		ob_end_clean();
		
		header('Content-Type: image/jpeg');
		print $data;
		exit;
	}

	public function check_folder_img( $root_folder = '', $root_date = "", $folder_by_date = 1, $thumb = "thumbnail" )
	{
		global $CMS;

		$root_folder = $CMS->vars['upload_dir']."/".$root_folder;

        //Check root folder
        if( ! is_dir ( $root_folder ) )
        {
            mkdir($root_folder, 0777);
            chmod( $root_folder, 0777 );
            if ( !is_writable($root_folder) ) { exit("EzyPHP: Please double check permission in {$root_folder}"); } //LHL-2018-06-08
        }

		if($folder_by_date == 1)
		{
			//Get date
			if ( !$root_date )
			{
				$today = date("d/m/Y");
				$today = explode("/", $today);
			}
			else
			{
				$today = explode("/", $root_date);
			} 

			//Check year
			$root_year = $root_folder."/".$today[2]."/";
			if( ! is_dir ( $root_year ) )
			{
				mkdir( $root_year, 0777 );
				chmod( $root_year, 0777 );
			}

			//check month
			$root_month = $root_folder."/".$today[2]."/".$today[1]."/";
			if( ! is_dir ( $root_month ) )
			{
				mkdir( $root_month, 0777 );
				chmod( $root_month, 0777 );
			}

			//check day
			$root_day = $root_folder."/".$today[2]."/".$today[1]."/".$today[0]."/";
			if( ! is_dir ( $root_day ) )
			{
				mkdir( $root_day, 0777 );
				chmod( $root_day, 0777 );
			}
		}
		else
		{
			$root_day = $root_folder."/";
		}


		$thumb_folder = $root_day."{$thumb}/";

		if( ! is_dir( $thumb_folder ) )
		{
			mkdir( $thumb_folder, 0777 );
			chmod( $thumb_folder, 0777 );
		}

		if($folder_by_date == 1)
		{
			return $today[2]."/".$today[1]."/".$today[0];
		}
		else
		{
			return "";
		}

	}


	public function check_folder_ggauth()
	{
		global $CMS;

		$root_folder = root_path."db/googleauth";
 
		//Check root folder
		if( ! is_dir ( $root_folder ) )
		{
			mkdir( $root_folder, 0777 );
			chmod( $root_folder, 0777 );
		}	 
		return "";
	}


	public function convert_to_thumbnail($image="", $thumb="thumbnail")
	{
		global $CMS;

		// Array ( [0] => 2016 [1] => 04 [2] => 14 [3] => 1460604280_8c82103cdb8f99b6a4f06549a13f82f1.jpg )
		//  ---->
		// Array ( [0] => 2016 [1] => 04 [2] => 14 [3] => thumbnail [4] => 1460604280_8c82103cdb8f99b6a4f06549a13f82f1.jpg )

		$thumb_file = explode("/",$image);

		array_splice($thumb_file, count($thumb_file)-1, 0, $thumb);
		$thumb_file = implode("/",$thumb_file);

		return $thumb_file;
	}

	public function uploadImgBase64($img, $upload_dir, $thumb_folder = "thumbnail", $thum_width=450, $prefix = "")
	{
		global $CMS;

		//Xác đinh đuôi file hình
		preg_match('/data:image\/([a-zA-Z]+);base64,/',$img, $match);

		$ext = $match[1];

		if(!$ext || !$upload_dir) return false;

		$img = str_replace('data:image/'.$ext.';base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);

		$file_name = empty($prefix) ? md5(microtime().rand(0,999999)).".{$ext}" : $prefix.rand(0,999999).'.'.$ext;
		$file = "$upload_dir/{$file_name}";
		//Check exit folder
		$CMS->class->image->is_dir($CMS->vars['upload_dir']."/".$upload_dir);

		$success = file_put_contents($CMS->vars['upload_dir']."/{$file}", $data );

		if($thumb_folder && $thum_width)
		{
			$CMS->class->image->is_dir($CMS->vars['upload_dir']."/".$upload_dir."/".$thumb_folder);

			$file_thumb = "$upload_dir/{$thumb_folder}/{$file_name}";
			$CMS->class->image->resize($CMS->vars['upload_dir']."/{$file}",$CMS->vars['upload_dir']."/{$file_thumb}", $thum_width);
			chmod($CMS->vars['upload_dir']."/{$file_thumb}", 0644);
		}

		if($success)
		{
			return $file_name;
		}
		else
		{
			return false;
		}
	}

	public function is_dir($dir = "", $chmod = 0755, $recursive = true)
	{
		if (!is_dir($dir)) {
		    mkdir($dir, $chmod, $recursive);

            if ( !is_writable($dir) ) { exit("EzyPHP: Please double check permission in {$dir}"); } //LHL-2018-07-22

		    return false;       
		}
		return true;
	}

    /**
     * Convert original url to thumbnail url
     * @param string $orgSrc
     * @param string $folder
     * @param string $sizePrefix
     * @param int $checkExisted
     * + 0:  Only convert url
     * + 1: Check if thumb not existed. Auto created new thumb
     * @param int $resizeWidth
     * @return bool|string
     */
    static function getThumb($orgSrc = "", $folder="thumbnail", $sizePrefix="", $checkExisted = 0, $resizeWidth=0, $resizeHeight=0)
    {
        global $CMS;

        $folder = empty($folder) ? 'thumbnail' : $folder;

        if(!$CMS->vars['optimize_image'] || !$resizeWidth)
        {
            return $orgSrc;
        }

        $explode = explode('/', $orgSrc);
        $explodeNum = count($explode);
        if(!$explodeNum) return false;

        //get file name
        $fileName = $explode[$explodeNum-1];
        $fileName = "{$sizePrefix}{$fileName}";
        $explode[$explodeNum-1] = $folder;

        //create folder if not existed
        $checkFolder = "{$CMS->vars['upload_dir']}/".implode('/',$explode);

        if( ! is_dir ( $checkFolder ) )
        {
            mkdir( $checkFolder, 0777, true );
            chmod( $checkFolder, 0777 );
        }

        $explode[$explodeNum] = $fileName;

        $thumbSrc = implode('/',$explode);

        if(!$checkExisted)
        {
            return $thumbSrc;
        }

        //Check thumbnail exist
        $imgPath = str_replace(array($CMS->vars['upload_url'], $CMS->vars['upload_dir']), '', $thumbSrc);
        $imgPath = trim($imgPath,'/');
        $imgPath = "{$CMS->vars['upload_dir']}/{$imgPath}";

        if(is_file($imgPath))
        {
            return $thumbSrc;
        }
        else
        {
            /**
             * Create thumbnail
             */
            $orgPath = str_replace(array($CMS->vars['upload_url'],$CMS->vars['upload_dir']), '', $orgSrc);
            $orgPath = trim($orgPath,'/');
            $orgPath = "{$CMS->vars['upload_dir']}/{$orgPath}";

            if(!is_file($orgPath)) return false;
            $CMS->class->image->resize($orgPath, $imgPath, $resizeWidth, $resizeHeight);
            return $thumbSrc;
        }
        //Check thumbnail exist
        /*if(is_file(str_replace($CMS->vars['upload_url'], $CMS->vars['upload_dir'], $thumbSrc)))
        {
            return $thumbSrc;
        }
        else
        {
            return $orgSrc;
        }*/
    }

    /**
     * @param $src
     * @return bool
     */
    static function checkExisted($src)
    {
        global $CMS;
        $path = "{$CMS->vars['upload_dir']}/{$src}";

        if(!file_exists($path) || !is_file($path))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * @param $src
     * @param string $noImageSrc
     * @return string
     */
    static function getSrc($src, $noImageSrc = "/acp/assets/img/no-photo.png")
    {
        global $CMS;

        if(!self::checkExisted($src))
        {
            return $noImageSrc;
        }
        else
        {
            return "{$CMS->vars['upload_url']}/{$src}";
        }
    }

    /**
     * @param $uploadFile
     * @param $dir
     * @param string $oldFile
     * @return bool|string
     */
    static function uploadFile($uploadFile, $dir, $oldFile = "")
    {
        global $CMS, $member;

        $time = time();
        $random = rand(0,100);

        if(!$uploadFile) return false;

        if($uploadFile['error'] != 4) //No file
        {
            if($uploadFile['error'] > 0) //Check file error
            {
                return false;
            }
            else
            {
                $user_id = !isset($member['user_id']) ? null : $member['user_id'];
                $file_src = "{$CMS->class->image->check_folder_img($dir,"",1)}/{$user_id}_{$time}_{$random}_{$CMS->class->seo->remove_vietnamese($uploadFile['name'])}";
                $path = "{$CMS->vars['upload_dir']}/{$dir}/{$file_src}";
                @copy($uploadFile['tmp_name'], $path) or die ("Could not be upload.");
                if($oldFile)
                {
                    @unlink("{$CMS->vars['upload_dir']}/{$dir}/{$oldFile}");
                }
                return $file_src;
            }
        }
        else
        {
            return $oldFile;
        }
    }

    // save img from url
    static function convertImageFromUrlToBase64($url)
    {
        $url = trim($url);

        $file_name = explode("/", $url);
        $file_name = $file_name[count($file_name)-1];

        // check type
        $imgs_extension = explode(".", $url);
        $imgs_extension = $imgs_extension[count($imgs_extension)-1];

        if(!$url)
        {
            return false;
        }

        $b64image = file_get_contents($url);
        $b64image = base64_encode($b64image);
        $b64image = "data:image/".$imgs_extension.";base64,".$b64image;

        return ['file_name' => $file_name, 'file_ext' => $imgs_extension, 'file_src' => $b64image];
    }

    public function remove_all_file( $dir = '', $thumb = "" )
	{
		global $CMS;
		if($dir != "")
		{
			if (is_dir($dir)) {
				$files = glob( 	$dir.'/*'); // get all file names
				foreach($files as $file){ // iterate files
				  if(is_file($file))
				    @unlink($file); // delete file
				}
				
			}

			if($thumb != "")
			{
				if (is_dir($dir."/".$thumb)) {
					$files = glob( 	$dir.'/'.$thumb.'/*'); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file))
					    @unlink($file); // delete file
					}
					
				}
			}
			
			return true;
		}
		return false;

	}

    /**
     * Quick upload image
     * nkvp - 2018/07/20
     * @param $file: file source
     * @param $dir: root directory
     * @param null $newName: prefix for new file name
     * @param null $removeOldFile: path to remove old image
     * @return array
     */
    function uploadImage($file, $dir, $newName=null, $removeOldFile=null)
    {
        global $CMS;

        $error = [];

        if($file['error'] != 0)
        {
            $error[] = $this->phpFileUploadErrors[$file['error']];
        }
        else
        {
            if(!$CMS->class->attachment->check_is_image($file['tmp_name']))
            {
                $error[] = "File is not a image";
            }
        }

        if(count($error))
        {
            $return = [
                'status' => 'fail',
                'msg' => implode(". ", $error)
            ];
        }
        else
        {
            $fileExt = $CMS->class->attachment->get_ext($file['name']);
            $name = $newName ? $newName : $file['name'];
            $newFileName = $CMS->class->seo->cleanurl($name).'-'.time().'-'.rand(0,9999).".{$fileExt}";
            $folder = $this->check_folder_img($dir,"",1,"thumbnail");

            $uploadPath = "{$dir}/{$folder}/{$newFileName}";

            if(copy($file['tmp_name'], "{$CMS->vars['upload_dir']}/{$uploadPath}"))
            {
                if(self::checkExisted($removeOldFile))
                {
                    unlink($CMS->vars['upload_dir']."/".$removeOldFile);
                }

                $return = [
                    'status' => 'success',
                    'file' => $uploadPath
                ];
            }
            else
            {
                $return = [
                    'status' => 'fail',
                    'msg' => implode(". ", $error)
                ];
            }
        }

        return $return;
    }

    /*
     * LHL-2018-08-11: Compare ratio, return true if ratio equal.
     */

    public function compare_ratio($src, $dest)
    {
        // Load files
        $src = $this->init($src, false);
        $dest = $this->init($dest, false);

        if ( imagesx($src)/imagesy($src) == imagesx($dest)/imagesy($dest) )
        {
            return true;
        }
        else
            {
            return false;
        }
    }
}

?>