<?php

namespace models;

use core\ezy;
use lib\input;
use lib\page;

class product
{
    private static $keys = [
        "product_id" => "id",
        "product_name" => "name",
        "product_name_lang" => "nameLang",
        "product_description" => "description",
        "product_description_lang" => "descriptionLang",
        "product_information_1" => "information1",
        "product_information_1_lang" => "information1Lang",
        "product_information_2" => "information2",
        "product_information_2_lang" => "information2Lang",
        "product_code" => "code",
        "product_group" => "group",
        "product_tax" => "tax",
        "product_price_sell" => "price",
        "product_price_original" => "oldPrice",
        "product_image" => "image",
        "product_image_alt" => "imageAlt",
        "product_gallery" => "gallery",
        "product_shorturl" => "slug",
        "product_shorturl_lang" => "slugLang",
        "product_type" => "type",
        "staff_id" => "staffIds",
        "product_estimated_time" => "estimatedTime",
    ];

    static public function getProducts($order="id", $by="asc")
    {
        global $CMS, $DB;

        $keys = array_flip(self::$keys);

        $order = $keys[$order] ? $keys[$order] : 'product_id';
        $by = !in_array(strtolower($by), ['asc','desc']) ?  'asc' : $by;

        $sql = "SELECT * FROM " . root_table . "product WHERE product_deleted=0 AND product_show=1 AND product_type=1 ORDER BY {$order} {$by}";
        $results = page::init($sql, 0, true, $cache_prefix = 'product');

        if ($results) {
            foreach ($results as $key => $result) {

                $result['product_price_sell'] *= 1;
                $result['product_price_original'] *= 1;
                $result['product_tax'] *= 1;
                $result['product_id'] *= 1;
                $result['product_group'] *= 1;
                $result['product_type'] *= 1;
                $result['product_estimated_time'] *= 1;
                $result['staff_id'] = input::jsonDecode($result['staff_id']);
                $result['staff_id'] = is_array($result['staff_id']) && $result['staff_id'] ? array_values($result['staff_id']) : [];

                $results[$key] = self::convertToDisplay($result);
            }
        }
        return $results;
    }

    static function getProduct($record_id=0, $field_return="*", $sql_add="")
    {
        global $DB, $CMS;

        if($record_id and $field_return)
        {
            // Count field
            $countField = count(explode(",", $field_return));

            //Query
            $data = $DB->fetch_data("SELECT {$field_return} FROM ".root_table."product WHERE {$sql_add} product_deleted=0 AND  AND product_type=1 AND (product_id='{$record_id}' OR product_shorturl='{$record_id}') LIMIT 1",'product')[0];

            //Check return
            if($countField == 1 and $field_return != "*")
            {
                return $data[$field_return];
            }else
            {
                return $data;
            }
        }else
        {
            return false;
        }
    }

    /**
     * convert key data for api
     * @param $result
     */
    static public function convertKeys($data = [])
    {
        global $CMS, $DB;

        $return = [];

        $keys = self::$keys;

        foreach ($data as $key => $value) {
            if (isset($keys[$key])) {
                $return[$keys[$key]] = $value;
            }
        }

        return $return;
    }

    static function convertToDisplay($data = [])
    {
        global $CMS;

        $data = self::convertKeys($data);

        $oriData = $data;

//        $data['price'] = $CMS->class->input->currency($oriData['price']);
//        $data['oriPrice'] = $CMS->class->input->currency($oriData['oriPrice']);

        if ($CMS->vars['translations']) {
            $name = @json_decode($oriData['name_lang'], true);
            $data['name'] = $name ? $name : $oriData['name'];

            $shorturl = @json_decode($oriData['slug_lang'], true);
            $data['slug'] = $shorturl ? $shorturl : $oriData['slug'];

            $description = @json_decode($oriData['description_lang'], true);
            $data['description'] = $description ? $description : $oriData['description'];// of news

            $information_1 = @json_decode($oriData['information_1_lang'], true);
            $data['information1'] = $information_1 ? $information_1 : (isset($oriData['information_1_lang']) ? $oriData['information_1'] : null);

            $information_2 = @json_decode($oriData['information_2_lang'], true);
            $data['information2'] = $information_2 ? $information_2 : (isset($oriData['information_2_lang']) ? $oriData['information_2'] : null);

            if (is_array($data['name'])) {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    if ($langCode == $CMS->vars['default_language']) {
                        $data['name'] = $data['name'][$langCode];
                        break;
                    }
                }
            }

            if (is_array($data['slug'])) {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    if ($langCode == $CMS->vars['default_language']) {
                        $data['slug'] = $data['slug'][$langCode];
                        break;
                    }
                }
            }

            if (is_array($data['description'])) {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    if ($langCode == $CMS->vars['default_language']) {
                        $data['description'] = html_entity_decode($data['description'][$langCode]);
                        break;
                    }
                }
            }

            if (is_array($data['information1'])) {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    if ($langCode == $CMS->vars['default_language']) {
                        $data['information1'] = html_entity_decode($data['information1'][$langCode]);
                        break;
                    }
                }
            }

            if (is_array($data['information2'])) {
                //Neu k phai dang mang thi chuyen ve mang
                foreach ($CMS->vars['translations'] as $langCode => $langName) {
                    if ($langCode == $CMS->vars['default_language']) {
                        $data['information2'] = html_entity_decode($data['information2'][$langCode]);
                        break;
                    }
                }
            }

        } else {
            $data['description'] = html_entity_decode(isset($data['description']) ? $data['description'] : '');
            $data['information1'] = html_entity_decode(isset($data['information1']) ? $data['information1'] : null);
            $data['information2'] = html_entity_decode(isset($data['information2']) ? $data['information2'] : null);
        }

        // Name
        $data['name'] = html_entity_decode($data['name'], ENT_QUOTES | ENT_XML1, 'UTF-8');

        //Check thumb
        $data['image'] = isset($oriData['image']) ? $oriData['image'] : '';
        $path = "product/{$data['image']}";

        //Sise L
        $thumb = \lib\image::getThumb($path, "thumbnail", 'L_', 1, 550);
        $data['imageL'] = input::checkImage($thumb, $CMS->vars['public_url'].'/images/no-image.png');

        //Sise M
        $thumb = \lib\image::getThumb($path, "thumbnail", 'M_', 1, 300);
        $data['imageM'] = input::checkImage($thumb, $CMS->vars['public_url'].'/images/no-image.png');

        //Sise S
        $thumb = \lib\image::getThumb($path, "thumbnail", 'S_', 1, 150);
        $data['imageS'] = input::checkImage($thumb, $CMS->vars['public_url'].'/images/no-image.png');

        // List gallery
        $data['gallery'] = isset($oriData['gallery']) ? $oriData['gallery'] : null;
        $data['gallery'] = json_decode($data['gallery'], true);

        $listGallery = array();

        if (!empty($data['gallery'])) {
            $i = 0;
            foreach ($data['gallery'] as $gallery) {
                // pathUpload: use for seo get thumb
                $listGallery[$i]['pathUpload'] = $gallery;

                //Sise L
                $thumb = \lib\image::getThumb($gallery, "thumbnail", 'L_', 1, 550);
                $listGallery[$i]['imageL'] = input::checkImage($thumb);

                //Sise M
                $thumb = \lib\image::getThumb($gallery, "thumbnail", 'M_', 1, 300);
                $listGallery[$i]['imageM'] = input::checkImage($thumb);

                $i++;
            }
        }

        $data['gallery'] = $listGallery;

        $data['oriData'] = $oriData;

        return $data;
    }
}