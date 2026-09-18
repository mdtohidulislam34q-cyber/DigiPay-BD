<?php

use User\Models\UserModel;

if (!function_exists("user_url")) {
    function user_url($url = ''): string
    {

        return base_url('user/' . $url);
    }
}
if (!function_exists("admin_url")) {
    function admin_url($url = ''): string
    {
        if (strpos($url, '.') !== false) {
            return url_to($url);
        }
        return base_url('admin/' . $url);
    }
}


if (!function_exists("current_user")) {
    function current_user($record = '', $id = "")
    {
        if (empty($id)) {
            $id = session('uid');
        }

        $userModel = new UserModel();
        $user = $userModel->get("*", 'users', ['id' => $id]);

        if (!empty($user)) {
            if (!empty($record)) {
                if (property_exists($user, $record)) {
                    return $user->$record;
                }
                return null;
            }
            return $user;
        }
        return null;
    }
}

if (!function_exists("fetch_user")) {
    function fetch_user($condition = [])
    {
        // Initialize the cache service
        $cache = \Config\Services::cache();

        if (empty($id)) {
            $id = session('uid');
        }

        // Check if the user data exists in cache
        $cacheKey = 'user_fetchs' . $id;
        if ($cachedData = $cache->get($cacheKey)) {
            $user = $cachedData;
        } else {
            $userModel = new UserModel();
            $user = $userModel->fetch("*", 'users', $condition);
            // $cache->save($cacheKey, $user, 3600);
        }

        if (!empty($user)) {
            return $user;
        }
        return null;
    }
}
if (!function_exists("fetch_plan")) {
    function fetch_plan($condition = [])
    {
        // Initialize the cache service
        $cache = \Config\Services::cache();

        // Check if the user data exists in cache
        $cacheKey = 'plan_fetch';
        if ($cachedData = $cache->get($cacheKey)) {
            $user = $cachedData;
        } else {
            $userModel = new UserModel();
            $user = $userModel->fetch("*", 'plans', $condition);
            $cache->save($cacheKey, $user, 3600);
        }

        if (!empty($user)) {
            return $user;
        }
        return null;
    }
}



if (!function_exists("current_admin")) {
    function current_admin($record = '', $id = "")
    {
        $cache = \Config\Services::cache();

        if (empty($id)) {
            $id = session('sid');
        }

        // Check if the admin data exists in cache
        $cacheKey = 'admin_' . $id;
        if ($cachedData = $cache->get($cacheKey)) {
            $admin = $cachedData;
        } else {
            $userModel = new UserModel();
            $admin = $userModel->get("*", 'staffs', ['id' => $id]);

            $cache->save($cacheKey, $admin, 600);
        }

        if (!empty($admin)) {
            if (!empty($record)) {
                if (property_exists($admin, $record)) {
                    return $admin->$record;
                }
                return null;
            }
            return $admin;
        }
        return null;
    }
}

function get_tree_table($uid, $maxLevel = 3, $level = 1)
{
    if ($level > $maxLevel) {
        return '';
    }

    // Fetch the users who have the given user ID as their referrer.
    $users = fetch_user(['ref_id' => $uid]);

    if (empty($users)) {
        return '';
    }

    $html = "<tr class='row'>";
    foreach ($users as $user) {
        $html .= "<td class='col-md-2 col-4'>";
        $html .= "<a href='#'>";
        $html .= "<img src='" . get_avatar('user', $user->id) . "' alt='Avatar' class='avatar' style='width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ccc;'>";
        $html .= "<div>" . htmlspecialchars($user->email) . "</div>";
        $html .= "</a>";
        $html .= "</td>";
    }
    $html .= "</tr>";

    // Recursively add the children rows
    foreach ($users as $user) {
        $html .= get_tree_table($user->id, $maxLevel, $level + 1);
    }

    return $html;
}

function render_user_tree_table($uid, $max)
{
    // Assuming `current_user` returns the user object of the currently logged-in user.
    $parentUser = current_user();
    if (!$parentUser) {
        return '<div>No data available for this user</div>';
    }

    $html = "<table class='user-tree-table'>";
    $html .= "<tr>";
    $html .= "<td>";
    $html .= "<a href='#'>";
    $html .= "<img src='" . get_avatar('user', $parentUser->id) . "' alt='Avatar' class='avatar' style='width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ccc;'>";
    $html .= "<div>" . htmlspecialchars($parentUser->email) . "</div>";
    $html .= "</a>";
    $html .= "</td>";
    $html .= "</tr>";

    // Call to the recursive function to build the user tree
    $html .= get_tree_table($parentUser->id, $max);

    $html .= "</table>";

    return $html;
}




if (!function_exists('get_active_plan')) {
    function get_active_plan($uid = '')
    {
        $uid = !empty($uid) ? $uid : session('uid');
        $userModel = new UserModel();
        $plan = $userModel->get("*", 'user_plans', ['uid' => $uid], 'id');
        if (!empty($plan) && !hasExpired($plan->expire)) {
            return $plan;
        }
        return false;
    }
}
if (!function_exists('get_expirydate_plan')) {
    function get_expirydate_plan($id)
    {
        $userModel = new UserModel();
        $plan = $userModel->get('id,expire', 'user_plans', ["plan_id" => $id, 'uid' => session('uid')], "id");
        return time_format($plan->expire);
    }
}

if (!function_exists('time_format')) {
    function time_format($dateTime)
    {
        $date = new DateTime($dateTime);
        return $date->format('j F, Y H:i:s A');
    }
}

if (!function_exists('plan_button')) {
    function plan_button($plan_id)
    {
        $userModel = new UserModel();
        $slect_plan = $userModel->get('id,ids,sort', 'plans', ["id" => $plan_id], "id");

        $link = user_url('buy-plan/' . $slect_plan->ids);
        $btn_class = "btn btn-primary d-grid w-100";
        $btn_name = "Buy Plan";
        $prefix  = '';
        $active = get_active_plan();
        if ($active) {
            $active_plan = $userModel->get('id,ids,sort', 'plans', ["id" => $active->plan_id], "id", "DESC");

            if ($active_plan->sort == $slect_plan->sort && $active_plan->id == $slect_plan->id) {
                $btn_class = "btn btn-success d-grid w-100";
                $btn_name  = "Buy Again";
                $prefix    = '<span class="badge bg-label-success ">Current Plan:<span class="d-none getExpire">' . $active->expire . '</span> <span class="m-2 text-danger countdown"></span></span>';
            } elseif ($active_plan->sort > $slect_plan->sort) {
                $btn_class = "btn btn-info d-grid w-100";
                $btn_name  = "Downgrade Plan";
            } elseif ($active_plan->sort < $slect_plan->sort) {
                $btn_name  = "Upgrade Plan";
            }
        }

        $xhtml = null;
        $xhtml = sprintf('%s<a href="%s" class="%s ajaxModal">%s</a>', $prefix, $link, $btn_class, $btn_name);
        return $xhtml;
    }
}

function is_valid_domain($url)
{
    $urlparts = parse_url(filter_var($url, FILTER_SANITIZE_URL));

    if (!isset($urlparts['host'])) {
        $urlparts['host'] = $urlparts['path'];
    }

    if ($urlparts['host'] != '' && !isset($urlparts['scheme'])) {
        return checkdnsrr($urlparts['host'], 'A');
    }

    return false;
}

if (!function_exists('canAddDevice')) {
    function canAddDevice($addedDevices)
    {
        if (!get_active_plan()) {
            return false;
        }
        $currentDate = time();
        $device = count($addedDevices);

        $remainingDevice = get_active_plan()->device - $device;

        if ($remainingDevice > 0 || get_active_plan()->device == -1) {
            return true;
        }

        return false;
    }
}
if (!function_exists('canAddBrand')) {
    function canAddBrand($addedBrand)
    {
        if (!get_active_plan()) {
            return false;
        }
        $currentDate = time();
        $brands = count($addedBrand);
        $remainingBrand = get_active_plan()->brand - $brands;

        if ($remainingBrand > 0 || get_active_plan()->brand == -1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('canAddTransaction')) {
    function canAddTransaction($addedTrx)
    {
        if (!get_active_plan()) {
            return false;
        }
        $currentDate = time();
        $transaction = count($addedTrx);
        $remainingTransaction = get_active_plan()->transaction - $transaction;

        if ($remainingTransaction > 0 || get_active_plan()->transaction == -1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('deviceValidation')) {
    function deviceValidation($device_key, $uid = '')
    {
        if (empty($uid)) {
            $uid = session('uid');
        }
        if (!get_active_plan($uid)) {
            return false;
        }

        $userModel = new UserModel();
        $available = 0;
        $plan = get_active_plan($uid);
        $devices = $userModel->fetch('*', 'devices', ['uid' => $uid], '', '', '', '', true);
        $active_device_list = [];

        $currentDate = time();

        $startDate = strtotime($plan->created_at);
        $endDate = strtotime($plan->expire);
        if ($plan->device == '-1') {
            return true;
        }

        for ($i = 0; $i < $plan->device; $i++) {
            if (!empty($devices[$i])) {
                $active_device_list[] = $devices[$i]['device_key'];
            }
        }
        $index = array_search($device_key, $active_device_list);
        if ($index !== false) {
            return true;
        }
        return false;
    }
}


if (!function_exists('brandValidation')) {
    function brandValidation($brand_key, $uid = '')
    {
        if (empty($uid)) {
            $uid = session('uid');
        }
        if (!get_active_plan($uid)) {
            return false;
        }

        $userModel = new UserModel();
        $available = 0;
        $plan = get_active_plan($uid);
        $brands = $userModel->fetch('*', 'brands', ['uid' => $uid], '', '', '', '', true);
        $active_brand_list = [];

        $currentDate = time();

        $startDate = strtotime($plan->created_at);
        $endDate = strtotime($plan->expire);
        if ($plan->device == '-1') {
            return true;
        }

        for ($i = 0; $i < $plan->device; $i++) {
            if (!empty($brands[$i])) {
                $active_brand_list[] = $brands[$i]['brand_key'];
            }
        }
        $index = array_search($brand_key, $active_brand_list);
        if ($index !== false) {
            return true;
        }
        return false;
    }
}


if (!function_exists('get_name_of_files_in_dir')) {
    function get_name_of_files_in_dir($path, $file_types = ['.php'])
    {
        $name_of_files = [];
        if ($path != "" && is_dir($path)) {
            try {
                $dir = new DirectoryIterator($path);
                foreach ($dir as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        foreach ($file_types as $type) {
                            if (substr($fileinfo->getFilename(), -strlen($type)) === $type) {
                                $name_of_files[] = basename($fileinfo->getFilename(), $type);
                                break; // Once a match is found, no need to check other types
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                log_message('alert', "Error reading directory: " . $e->getMessage());
            }
        } else {
            // Log invalid directory path
            log_message('alert', "Invalid directory path: $path");
        }
        return $name_of_files;
    }
}


if (!function_exists("get_brand_data")) {
    function get_brand_data($brand_id = '', $id = "")
    {
        $help_model = new UserModel();
        $user = $help_model->get("*", 'brands', ['uid' => $id, 'id' => $brand_id]);
        if (!empty($user)) {
            return $user;
        } else {
            return false;
        }
    }
}


if (!function_exists('simple_post')) {
    function simple_post($url, $data)
    {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        $curl = curl_init();
        $data = http_build_query($data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_VERBOSE => true
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}

function payment_option($method = '', $option = 'logo')
{
    $userModel = new UserModel();
    $gateway = $userModel->get('*', 'payments', ['type' => $method])->params;
    $opt = get_value($gateway, 'option');
    return get_value($opt, $option);
}

if (!function_exists("current_plan")) {
    function current_plan($record = '', $id = "")
    {
        $userModel = new UserModel();
        $user = $userModel->get("*", 'plans', ['id' => $id]);

        if (!empty($user)) {
            if (!empty($record)) {
                if (property_exists($user, $record)) {
                    return $user->$record;
                }
                return null;
            }
            return $user;
        }
        return null;
    }
}
