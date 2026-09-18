<?php

if (!function_exists('show_empty_item')) {
    function show_empty_item()
    {
        $xhtml = null;
        $image_page = base_url('public/assets/img/no-result.svg');
        $content = lan("look_like_there_are_no_results_in_here");
        $xhtml = sprintf('<div class="col-md-12 data-empty text-center">
            <div class="my-content">
            <img class="img mb-1" src="%s" alt="Empty Data" height="%s"width="%s">
            <div class="title">%s</div>
            </div>
        </div>', $image_page, '20%', '20%', $content);
        return $xhtml;
    }
}
if (!function_exists('show_pagination')) {
    function show_pagination($pagination)
    {
        $xhtml = null;
        if (!empty($pagination)) {
            $xhtml .= sprintf('<div class="col-md-12"><div class="float-end">%s</div></div>', $pagination->links());
        }
        return $xhtml;
    }
}

if (!function_exists("ticket_status_title")) {
    function ticket_status_title($key)
    {
        switch ($key) {
            case 'new':
                return lan('New');
                break;
            case 'pending':
                return lan('Pending');
                break;

            case 'closed':
                return lan('Closed');
                break;

            case 'answered':
                return lan('Answered');
                break;
        }
    }
}
if (!function_exists('show_item_ticket_message_detail')) {
    function show_item_ticket_message_detail($controller_name, $item = [], $task = '')
    {
        $xhtml = null;
        $xhtml_footer = null;
        if (isset($item['support']) && $item['support']) {
            $class_item  = 'flex-row-reverse tr_' . $item['ids'];
            $img_class  = 'image-box ms-sm-4 ms-2 mb-4 float-end';
            $img_url          = get_avatar('admin');
            $type = 'sent';
            if ($task == 'user') {
                $edit_item_link = null;
                $delete_item_link = null;
            } else {
                $edit_item_link   = admin_url($controller_name . '/edit_item_ticket_message/' . $item['ids']);
                $delete_item_link = admin_url($controller_name . '/delete_item_ticket_message/' . $item['ids']);
                $xhtml_footer = sprintf(
                    '<div class="msg-footer p-t-5">
                        <a href="%s" class="ajaxModal btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Edit message">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="%s" class="ajaxDeleteItem btn btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Delete message" data-confirm_ms="Are you sure to delete this?" data-original-title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>',
                    $edit_item_link,
                    $delete_item_link
                );
            }
        } else {
            $class_item  = 'justify-content-start';
            $img_url = get_avatar('user', $item['id']);
            $img_class = 'image-box me-sm-4 me-2 float-start';
            $type = 'received';
        }
        $content = str_replace("\n", "<br>", esc($item['message']));
        $created = time_ago($item['created_at']);
        $author  = $item['first_name'];
        if (isset($item['author'])) {
            $author  = $item['author'];
        }
        $xhtml   = sprintf(
            '<div class="media m-2 %s align-items-%s ">
                <div class="%s"><img class="rounded-circle" height="40" src="%s" alt="Image Icon" /></div>
                <div class="message-%s">
                    <div>
                        <strong>%s</strong>
                        <span class="text-muted small"> %s </span>
                    </div>
                    <div class="msg-content"> %s </div>
                    %s
                </div></div>',
            $class_item,
            $class_item,
            $img_class,
            $img_url,
            $type,
            $author,
            $created,
            $item['message'],
            $xhtml_footer
        );
        return $xhtml;
    }
}

if (!function_exists('show_filter_status_button')) {
    function show_filter_status_button($controller_name, $items_status_button = [], $params = [], $type = '')
    {
        $xhtml = null;
        $config_status       = app_config('config')['status'];
        if ($items_status_button && count($items_status_button) > 0) {

            $current_tmpl_status = (in_array($controller_name, array_keys($config_status))) ? $controller_name . '_status' : 'status';
            $tmpl_status         = app_config('template')[$current_tmpl_status];

            $xhtml .= '<div class="btn-group w-30 m-b-10 ">';
            array_unshift($items_status_button, [
                'status' => 'all',
                'count'  => array_sum(array_column($items_status_button, 'count'))
            ]);

            $param_search = $params['search'];
            $current_search = array_combine(array_keys($param_search), array_values($param_search));
            foreach ($items_status_button as $key => $item) {
                if ($type == 'user') {
                    $link = user_url($controller_name) . '?status=' . $item['status'];
                } else {
                    $link = admin_url($controller_name) . '?status=' . $item['status'];
                }
                if ($current_search['query'] != "") {
                    $link .= '&' . http_build_query($current_search);
                }
                $current_status = (array_key_exists($item['status'], $tmpl_status)) ? $item['status'] : 'all'; //Default
                $current_class  = (get('status') == $item['status']) ? 'btn-primary' : '';
                $xhtml .= sprintf(
                    '<a href="%s" class="btn border %s">%s <span class="badge light badge-pill %s">%s</span></a>',
                    $link,
                    $current_class,
                    $tmpl_status[$current_status]['name'],
                    $tmpl_status[$current_status]['class'],
                    $item['count']
                );
            }
            $xhtml .= '</div>';
        }
        return $xhtml;
    }
}

if (!function_exists('show_search_area')) {
    function show_search_area($controller_name, $params, $task = 'admin')
    {
        $xhtml = null;
        $tmpl_search_fields   = app_config('template')['search_field'];
        $field_in_controller  = app_config('config')['search'];
        $current_controller = (array_key_exists($controller_name, $field_in_controller)) ? $controller_name : 'default';
        $param_search = $params['search'];
        $xhtml_fields = null;
        $class_btn_clear = (!empty($param_search['query'])) ? '' : 'd-none';
        $search_placeholder = lang("Search_for_");
        if ($task == 'admin') {
            $xhtml_fields = '<select name="field" class="form-control" id="">';
            foreach ($field_in_controller[$current_controller] as $item) {
                $selected = ($item == $param_search['field']) ? 'selected' : '';
                $xhtml_fields .= sprintf('<option value="%s" %s>%s</option>', $item, $selected,  $tmpl_search_fields[$item]['name']);
            }
            $xhtml_fields .= '</select>';
            $search_placeholder = 'Search for…';
        }
        $xhtml = sprintf(
            '<div class="form-group">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control" placeholder="%s" value="%s">
                        %s
                        <button class="btn btn-primary btn-search" type="button"><span class="fas fa-search"></span></button>
                        <button class="btn btn-outline-danger btn-clear %s" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Clear" type="button">X</button>
                    </div>
                </div>',
            $search_placeholder,
            $param_search['query'],
            $xhtml_fields,
            $class_btn_clear
        );
        return $xhtml;
    }
}


if (!function_exists('show_item_check_box')) {
    function show_item_check_box($type = null, $ids = '', $class_input = "check-all", $data_name = 'check_1')
    {
        $xhtml       = null;
        $xhtml_input = null;
        switch ($type) {
            case 'check_items':
                $xhtml_input = sprintf('<input type="checkbox" class="form-check-input check-items %s" data-name="%s">', $class_input, $data_name);
                break;
            case 'check_item':
                $xhtml_input = sprintf('<input type="checkbox" class="form-check-input check-item %s" name="ids[]" value="%s">', $data_name, $ids);
                break;
        }
        $xhtml = sprintf('<div class="custom-controls-stacked">
                            <label class="form-check">%s<span class="custom-control-label"></span>
                            </label>
                        </div>', $xhtml_input);
        return $xhtml;
    }
}

if (!function_exists('show_item_sort')) {
    function show_item_sort($controller_name, $id, $sort)
    {
        $xhtml = null;
        $link = admin_url($controller_name . '/change_sort/');
        $xhtml = sprintf('<input type="text" class="form-control text-center ajaxChangeSort" data-url="%s" data-id="%s" min="1" style="width:65px;" id="sort" value="%s">', $link, $id, $sort);
        return $xhtml;
    }
}
if (!function_exists('getAnchor')) {
    function getAnchor($message)
    {
        $anchor = '';
        $pattern = '/<a\s[^>]*href\s*=\s*(["\']??)([^"\'>]*)\\1[^>]*>.*?<\/a>/i';
        if (preg_match($pattern, $message, $matches)) {
            $extractedLink = $matches[0];
            $anchor = '<i class="bx bx-link"></i>' . $extractedLink;
        }
        return $anchor;
    }
}

if (!function_exists('show_item_status')) {
    function show_item_status($controller_name = '', $id = '', $status = '', $type = null, $task = null, $user = '')
    {
        $xhtml = null;
        switch ($type) {
            case 'switch':
                $link = $user == 'user' ? user_url($controller_name . '/change_status/') : admin_url($controller_name . '/change_status/');
                $checked = ($status) ? 'checked' : '';
                $xhtml = sprintf('<label class="custom-switch">      
                                    <input type="checkbox" name="item_status" data-id="%s" data-status="%s" data-action="%s" class="custom-switch-input ajaxToggleItemStatus" %s>
                                    <span class="custom-switch-indicator"></span>
                                </label>', $id, $status, $link, $checked);
                break;
            default:
                $config_status       = app_config('config')['status'];
                $current_tmpl_status = (in_array($controller_name, array_keys($config_status))) ? $controller_name . '_status' : 'status';
                if (in_array($controller_name, ['order', 'dripfeed', 'subscriptions', 'refill', 'affiliates'])) {
                    $tmpl_status         = app_config('template')['order_status'];
                } else {
                    $tmpl_status         = app_config('template')[$current_tmpl_status];
                }
                $current_tmpl_status = (array_key_exists($status, $tmpl_status)) ? $tmpl_status[$status] : $tmpl_status['1'];
                $status_name = $current_tmpl_status['name'];
                if ($task == 'user') {
                    $status_name = lang($status_name);
                }
                $xhtml = sprintf('<span class="badge %s">%s</span>', $current_tmpl_status['class'], $status_name);
                break;
        }
        return $xhtml;
    }
}


if (!function_exists('show_high_light')) {
    function show_high_light($input, $param_search = '', $field = '')
    {
        if ($param_search['query'] !== "") {
            if ($param_search['field'] == 'all' || $param_search['field'] == $field) {
                $input = preg_replace('#' . preg_quote($param_search['query']) . '#i', '<span class="bg-warning">\\0</span>', $input);
            }
        }
        return $input;
    }
}


if (!function_exists('show_item_datetime')) {
    function show_item_datetime($datetime = 'Asia/Dhaka', $type = 'long')
    {
        $datetime = convert_timezone($datetime);
        $new_datetime = date(app_config('template')['datetime'][$type], strtotime($datetime));
        return $new_datetime;
    }
}

if (!function_exists('show_bulk_btn_action')) {
    function show_bulk_btn_action($controller_name, $user = '', $trash = '')
    {
        $xhtml = null;
        $ml = '';
        $tmpl_buttons = app_config('template')['bulk_action'];
        $btn_area     = app_config('config')['bulk_action'];
        $curent_btn_area = (array_key_exists($controller_name, $btn_area)) ? $btn_area[$controller_name] : $btn_area['default'];

        if (!empty($trash)) {
            $trash_link = admin_url($controller_name . '/bulk_action/delete-all');
            $restore_link = admin_url($controller_name . '/bulk_action/restore');
            $ml .= sprintf('<a class="btn btn-success me-2 ajaxActionOptions" href="%s" data-type="restore">Restore %s</a>', $restore_link, $trash);
            $ml .= sprintf('<a class="btn btn-danger me-2 ajaxActionOptions" href="%s" data-type="delete-all">Clean %s</a>', $trash_link, $trash);
        }

        $xhtml .= '<div class="d-flex">';
        $xhtml .= $ml;
        $xhtml .= '<div class="item-action dropdown action-options">';
        $xhtml .= '<button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">Actions <span class="fe fe-chevrons-down"></span></button>';
        $xhtml .= '<div class="dropdown-menu dropdown-menu-right">';

        foreach ($curent_btn_area as $item) {
            $current_btn = $tmpl_buttons[$item];
            $link        = $user == 'user' ? user_url($controller_name . $current_btn['route-name'] . $item) : admin_url($controller_name . $current_btn['route-name'] . $item);
            $action_type = 'data-type="' . $item . '"';
            $xhtml .= sprintf('<a href="%s" %s class="dropdown-item %s"><i class="dropdown-icon %s"></i> %s</a>', $link, $action_type, $current_btn['class'], $current_btn['icon'], $current_btn['name']);
        }

        $xhtml .= '</div></div></div>';
        return $xhtml;
    }
}


if (!function_exists('render_table_thead')) {
    function render_table_thead($columns, $check_items = true, $show_number = true, $action =  true, $params = [])
    {
        $xhtml = '<thead><tr>';
        if (isset($params['sort-table']) && $params['sort-table']) {
            $xhtml .= '<th class="text-center w-1"><i class="fe fe-move"></i></th>';
        }
        if ($check_items) {
            $data_name = (isset($params['checkbox_data_name'])) ? $params['checkbox_data_name'] : 'check_1';
            $show_check_items = show_item_check_box('check_items', '', 'check-all', $data_name);
            $xhtml .= sprintf('<th class="text-center w-1">%s</th>', $show_check_items);
        }
        if ($show_number) {
            $xhtml .= '<th class="text-center w-1">Sl.</th>';
        }
        if (!empty($columns)) {
            foreach ($columns as $column) {
                $xhtml .= sprintf('<th class="%s">%s</th>', $column['class'], $column['name']);
            }
        }
        if ($action) {
            $xhtml .= '<th class="text-center">action</th>';
        }
        $xhtml .= '</tr></thead>';
        return $xhtml;
    }
}


if (!function_exists('show_item_button_action')) {
    function show_item_button_action($controller_name, $ids, $format = 'dropdown', $item_data = [], $user = '')
    {
        $xhtml = null;
        $tmpl_buttons = app_config('template')['button'];
        $btn_area = app_config('config')['button'];
        $curent_btn_area = (array_key_exists($controller_name, $btn_area)) ? $btn_area[$controller_name] : $btn_area['default'];

        switch ($format) {
            case 'btn-group':
                $xhtml .= '<div class="btn-group">';
                foreach ($curent_btn_area as $item) {
                    $current_btn = $tmpl_buttons[$item];
                    $link = $user == 'user' ? user_url($controller_name . $current_btn['route-name'] . $ids) : admin_url($controller_name . $current_btn['route-name'] . $ids);
                    $confirm_message = "";
                    if ($item == 'delete') {
                        $confirm_message = "delete this item";
                    }
                    $xhtml .= sprintf(
                        '<a href="%s" class="btn btn-icon btn-outline-info %s" data-confirm_ms="%s" data-bs-toggle="tooltip" data-placement="bottom" title="%s">
                            <i class="%s"></i>
                        </a>',
                        $link,
                        $current_btn['class'],
                        $confirm_message,
                        $current_btn['name'],
                        $current_btn['icon']
                    );
                }
                $xhtml .= '</div>';
                break;

            default:
                $xhtml .= '<div class="item-action dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="icon"><i class="fa fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu">';
                foreach ($curent_btn_area as $item) {
                    $current_btn = $tmpl_buttons[$item];
                    $link = $user == 'user' ? user_url($controller_name . $current_btn['route-name'] . $ids) : admin_url($controller_name . $current_btn['route-name'] . $ids);
                    $confirm_message = "";
                    if ($item == 'delete') {
                        $confirm_message = "delete this item";
                    }
                    $xhtml .= sprintf('<a href="%s" class="dropdown-item %s" data-confirm_ms="%s"><i class="dropdown-icon %s"></i> %s</a>', $link, $current_btn['class'], $confirm_message, $current_btn['icon'], $current_btn['name']);
                }
                $xhtml .= '</div></div>';
                break;
        }
        return $xhtml;
    }
}

if (!function_exists('convert_string_number_list_to_array')) {
    function convert_str_number_list_to_array($str)
    {
        $ar = [];
        if (!is_string($str)) {
            return $ar;
        }
        $str = rtrim($str, ',');
        $str = ltrim($str, ',');
        return $ar = explode(',', $str);
    }
}

if (!function_exists('show_item_ticket_subject')) {
    function show_item_ticket_subject($controller_name, $item_data, $params = [])
    {
        $xhtml = null;
        $xhtml_un_read = null;
        if ($item_data['is_admin_read'] == 0) {
            $xhtml_un_read = '<span class="badge bg-info">Unread</span>';
        }
        $link    = admin_url($controller_name . '/view/' . $item_data['ids']);
        $subject = show_high_light(esc($item_data['subject']), $params['search'], 'subject');

        $xhtml   = sprintf('<a href="%s">%s %s</a>', $link, $subject, $xhtml_un_read);
        return $xhtml;
    }
}

if (!function_exists('show_view_ticket_button_group')) {
    function show_view_ticket_button_group($controller_name, $item = [])
    {
        $xhtml = null;
        $xhtml_dropdown = null;
        $closed_link = admin_url($controller_name . "/change_status/closed/" . $item['ids']);
        $dropdowns = [
            'answered' =>  'Mark as Answered',
            'pending'  =>  'Mark as Pending',
            'unread'   =>  'Mark as Unread',
        ];
        if ($dropdowns) {
            $xhtml_dropdown = '<div class="btn-group" role="group"> 
            <button id="btnGroupDrop1" type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">';
            foreach ($dropdowns as $key => $dropdown) {
                $link = admin_url($controller_name . "/change_status/" . $key . '/' . $item['ids']);
                $xhtml_dropdown .= sprintf('<a href="%s" class="dropdown-item">%s</a>', $link, $dropdown);
            }
            $xhtml_dropdown .= '</div></div>';
        }
        $xhtml   = sprintf(
            '<div class="btn-group float-end m-3" role="group" aria-label="Actions Group">
                <a href="%s" class="btn btn-outline-primary">Close ticket</a>
                %s
            </div>',
            $closed_link,
            $xhtml_dropdown
        );
        return $xhtml;
    }
}


if (!function_exists('get_addon_details')) {
    function get_addon_details($addon = '')
    {
        $xhtml = null;
        $dir = APPPATH . "Modules/Blocks/Addons/" . $addon;
        try {
            $file = searchFileInFolder($dir, 'info.json');
            if ($file) {
                $data = get_json_content_from_file($file);
            }
            $name = isset($data['name']) ? $data['name'] : $addon;
            $description = isset($data['description']) ? $data['description'] : $addon;
            $logo = isset($data['logo']) ? $data['logo'] : get_logo();
            $xhtml   = sprintf(
                '<div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                    <div class="ms-3">
                    <img class="img-fluid rounded-circle" src="%s">
                        <p>%s</p>
                        <h6 class="mb-0">%s</h6>
                    </div>
                </div>',
                $logo,
                $description,
                $name
            );
            return $xhtml;
        } catch (Exception $e) {
            $xhtml   = sprintf(
                '<div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-chart-bar fa-3x text-primary"></i>
                    <div class="ms-3">
                        <p class="mb-2">Addon</p>
                        <h6 class="mb-0">%s</h6>
                    </div>
                </div>',
                $addon
            );
            return $xhtml;
        }
    }
}

if (!function_exists('duration_type')) {
    function duration_type($name, $type, $duration, $badge = true, $show_name = false)
    {
        $xhtml = null;
        $duration = ($duration == -1) ? "Unlimited" : $duration;
        switch ($type) {
            case '1':
                $type = 'Days';
                $status = 'success';
                break;
            case '2':
                $type = 'Months';
                $status = 'info';
                break;
            case '3':
                $type = 'Years';
                $status = 'warning';
                break;

            default:
                $type = 'Not Identified';
                $status = 'danger';
                break;
        }
        if (!$show_name) {
            $name = '';
        }

        $badge_class = ($badge) ? "badge bg-" . $status : '';


        $xhtml = sprintf('%s <small class="ml-auto %s">%s %s</small>', $name, $badge_class, $duration, $type);
        return $xhtml;
    }
}

if (!function_exists('plan_message')) {
    function plan_message($key, $count)
    {
        $xhtml = null;
        $prefix = ucfirst($key) . ':  ';
        switch ($count) {
            case '-1':
                $tooltip = "You can add Unlimited " . plural($key);
                $message = $prefix . '&#8734; ' . plural($key);
                break;
            case '1':
                $tooltip = "You can add maximum 1 " . $key;
                $message = $prefix . '1 ' . $key;
                break;

            default:
                $tooltip = "You can add maximum " . $count . ' ' . plural($key);
                $message = $prefix . $count . ' ' . plural($key);
                break;
        }

        $xhtml = sprintf('%s <i class="bx bx-info-circle bx-xs" data-bs-toggle="tooltip" data-bs-placement="top" title="%s"></i>', $message, $tooltip);
        return $xhtml;
    }
}
