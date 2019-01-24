<?php

use \core\ezy;
use lib\input;

if (!defined('IN_ROOT')) exit();
ezy::load_model('work_schedule');
ezy::load_model('shift_work');
ezy::load_model('city', 'booking_data');
ezy::load_model('store', 'booking_data');

new work_schedule;

class work_schedule
{
    public $html;

    /**
     * work_schedule constructor.
     */
    public function __construct()
    {
        global $CMS, $DB, $member, $tpl;

        //url decode

        foreach ($CMS->input as $k => $v) {
            $CMS->input[$k] = !is_array($CMS->input[$k]) ? urldecode($v) : $CMS->input[$k];
        }

        $CMS->class->language->load("work_schedule");

        switch ($CMS->input['act']) {
            case 'export':
                $this->export();
                break;
            default:
                switch (input::get('subact')) {
                    case 'load_data':
                        $this->load_data();
                        break;
                    case 'load_stores':
                        $this->load_stores();
                        break;
                    case 'update_slot':
                        $this->update_slot();
                        break;
                    case 'clone':
                        $this->cloneData();
                        break;
                    case 'clear_cache':
                        $CMS->class->cache->mdelete('work_schedule');
                        $_SESSION['msg'] = $CMS->lang['cleared_cache_module'];
                        $CMS->global->redirect("{$CMS->vars['root_domain']}/?site={$CMS->input['site']}");
                        break;
                    default:
                        $this->default_page();
                        break;
                }
                break;
        }
    }

    /**
     * Main page
     */
    public function default_page()
    {
        global $CMS, $tpl;

        $CMS->core->page_title = $CMS->lang['title'];

        if (input::get('subact') == 'update') {
            $input = $_POST;
            $checkValid = \models\work_schedule::validate($input);

            if ($checkValid['valid']) {
                //Clean data before update
                \models\work_schedule::cleanData($input['work_schedule_date'], $input['store_id']);

                foreach ($input['staff'] as $staff_id => $shift_work) {
                    foreach ($shift_work as $shift_work_id) {
                        if ($shift_work_id *= 1) {
                            $data = [
                                'work_schedule_date' => $input['work_schedule_date'],
                                'store_id' => $input['store_id'],
                                'staff_id' => $staff_id,
                                'shift_work_id' => $shift_work_id
                            ];

                            \models\work_schedule::add($data);
                        }
                    }
                }
                input::jsonEncode(['status' => 'success', 'msg' => 'Đã cập nhật lịch trực ngày ' . $input['work_schedule_date']]);
            } else {
                input::jsonEncode(['status' => 'fail', 'msg' => $checkValid['msg']]);
            }
        }

        $tpl->data = \models\work_schedule::addValue($CMS->input);
        $tpl->shift_work = \models\shift_work::getAll("", 'shift_work_order', 'desc');
        $tpl->cities = \models\city::getCities();
        $tpl->store = [];
        $tpl->users = $CMS->user->load_list_staff('', 'array');

        if (is_array($tpl->users) && $tpl->users) {
            $stores = \models\store::getStores();
            foreach ($tpl->users as $key => $user) {
                if ($user['store_id']) {
                    $i = $stores ? array_search($user['store_id'], array_column($stores, 'id')) : -1;
                    $user['store'] = input::arrayValue($stores, $i, null);
                } else {
                    $user['store'] = null;
                }

                $tpl->users[$key] = $user;
            }
        }

        // Output data
        $CMS->output .= ezy::html("form");
    }

    function load_data()
    {
        global $CMS;
        $data = \models\work_schedule::loadData($CMS->input['date']);
        input::jsonEncode($data);
    }

    function load_stores()
    {
        global $CMS;
        $city_id = $CMS->input['city_id'] * 1;
        $data = \models\store::getStores(" city_id = $city_id AND ");
        input::jsonEncode($data);
    }

    function update_slot()
    {
        global $CMS, $DB;

        $user_id = input::get("id") * 1;
        $slots = input::get("slots") * 1;

        $data = ['user_id' => $user_id, 'user_booking_slots' => $slots];

        //Check valid
        if ($slots < 1) {
            $return = [
                'status' => 'fail',
                'msg' => 'Slots phải lớn hơn 0',
            ];
            input::jsonEncode($return);
        }

        if ($DB->update("user", $data, "user_id")) {
            $return = [
                'status' => 'success',
                'msg' => 'Cập nhật thành công',
            ];
        } else {
            $return = [
                'status' => 'fail',
                'msg' => 'Cập nhật không thành công',
            ];
        }

        input::jsonEncode($return);
    }

    function cloneData()
    {
        global $CMS;

        //Dữ liệu nguồn để copy theo đúng store muốn lấy data
        $fromStore = input::get('from_store') * 1;
        $sql_add = "store_id = {$fromStore} AND";
        $fromData = \models\work_schedule::loadData($CMS->input['from_date'], $sql_add);
        $fromData = $fromData['data'];

        //Dữ liệu đích đến
        $toStore = input::get('to_store') * 1;
        $toData = \models\work_schedule::loadData($CMS->input['to_date']);

        //Lọc bỏ dữ liệu đích đến theo store_id của store đích để chuẩn bị so sánh với dữ liệu nguồn
        $toData = array_filter($toData['data'], function($x) use ($toStore) {
            return $x['store_id'] != $toStore;
        });

        //Tạo check sum cho data đích
        $toData = array_map(function($x) {
            $x['checksums'] = "{$x['shift_work_id']}_{$x['staff_id']}";
            return $x;
        }, $toData);

        //Chuyển store_id của dữ liệu được copy sang store mới
        $fromData = array_map(function ($x) use ($toStore) {
            global $CMS;
            $x['store_id'] = $toStore;

            //Tạo checksums để so sánh
            $x['checksums'] = "{$x['shift_work_id']}_{$x['staff_id']}";
            return $x;
        }, $fromData);

        //Lọc bỏ tiếp dữ liệu nguồn có trùng shift_work_id + staff_id + work_schedule_date (checksums). Do nhân viên không thể trục cùng 1 ca + 1 ngày ở nhiều store cùng lúc
        $toDataChecksums = array_column($toData, 'checksums');
        $fromData = array_filter($fromData, function($x) use ($toDataChecksums) {
            return !in_array($x['checksums'], $toDataChecksums);
        });

        $result = [
            'status' => 'success',
            'data' => [
                'store' => $toStore,
                'date' => $CMS->input['to_date'],
                'items' => array_merge($toData, $fromData)
            ]
        ];

        input::jsonEncode($result);
    }
}
