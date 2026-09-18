<?php

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

if (!function_exists('link_asset')) {
    function link_asset($url = '', $theme = '', $type = 'text/css')
    {
        $link = '<link ';

        $link .= ' type="' . $type . '" ';

        if ($theme == '') {
            $theme = 'public/assets/';
        }

        if (preg_match('#^([a-z]+:)?//#i', $url)) {
            $link .= 'href="' . $theme . $url . '" ';
        } else {
            $link .= 'href="' . base_url($theme . $url) . '" ';
        }
        return $link . " rel='stylesheet'/>\n";
    }
}

if (!function_exists('script_asset')) {
    function script_asset($src = '', $theme = '', $type = 'text/javascript')
    {
        $script = '<script ';

        $script .= ' type="' . $type . '" ';

        if ($theme == '') {
            $theme = 'public/assets/';
        }

        if (preg_match('#^([a-z]+:)?//#i', $src)) {
            $script .= 'src="' . $theme . $src . '" ';
        } else {
            $script .= 'src="' . base_url($theme . $src) . '" ';
        }
        return $script . "></script>\n";
    }
}

if (!function_exists('ms')) {
    function ms($array)
    {
        print_r(json_encode($array));
        exit(0);
    }
}
if (!function_exists('encrypt_encode')) {
    function encrypt_encode($text)
    {
        $encrypter = \Config\Services::encrypter();
        $encryptedData = $encrypter->encrypt($text);
        return base64_encode($encryptedData);
    }
}

if (!function_exists('encrypt_decode')) {
    function encrypt_decode($encodedText)
    {
        $encrypter = \Config\Services::encrypter();
        $binaryData = base64_decode($encodedText);
        return $encrypter->decrypt($binaryData);
    }
}
if (!function_exists("currency_format")) {
    function currency_format($number, $number_decimal = "", $decimalpoint = "", $separator = "")
    {
        $decimal = 2;

        if ($number_decimal == "") {
            $decimal = get_option('currency_decimal', 2);
        }

        if ($decimalpoint == "") {
            $decimalpoint = get_option('currency_decimal_separator', 'dot');
        }

        if ($separator == "") {
            $separator = get_option('currency_thousand_separator', 'comma');
        }

        switch ($decimalpoint) {
            case 'dot':
                $decimalpoint = '.';
                break;
            case 'comma':
                $decimalpoint = ',';
                break;
            default:
                $decimalpoint = ".";
                break;
        }

        switch ($separator) {
            case 'dot':
                $separator = '.';
                break;
            case 'comma':
                $separator = ',';
                break;
            default:
                $separator = ',';
                break;
        }
        $number = number_format($number, $decimal, $decimalpoint, $separator);
        return get_option('currency_symbol') . $number;
    }
}
if (!function_exists("currency_codes")) {
    function currency_codes()
    {
        $data = array(
            "AUD" => "Australian dollar",
            "BRL" => "Brazilian dollar",
            "BDT" => "Bangladeshi Tk",
            "CAD" => "Canadian dollar",
            "CZK" => "Czech koruna",
            "DKK" => "Danish krone",
            "EUR" => "Euro",
            "HKD" => "Hong Kong dollar",
            "HUF" => "Hungarian forint",
            "INR" => "Indian rupee",
            "ILS" => "Israeli",
            "JPY" => "Japanese yen",
            "MYR" => "Malaysian ringgit",
            "MXN" => "Mexican peso",
            "TWD" => "New Taiwan dollar",
            "NZD" => "New Zealand dollar",
            "NOK" => "Norwegian krone",
            "PHP" => "Philippine peso",
            "PLN" => "Polish złoty",
            "GBP" => "Pound sterling",
            "RUB" => "Russian ruble",
            "SGD" => "Singapore dollar",
            "SEK" => "Swedish krona",
            "CHF" => "Swiss franc",
            "THB" => "Thai baht",
            "USD" => "United States dollar",
        );

        return $data;
    }
}
if (!function_exists('set_session')) {
    function set_session($name, $input)
    {
        return session()->set($name, $input);
    }
}

if (!function_exists('unset_session')) {
    function unset_session($name)
    {
        return session()->remove($name);
    }
}

if (!function_exists('is_client_logged_in')) {
    function is_client_logged_in()
    {
        return session('uid') !== null;
    }
}

if (!function_exists('is_admin_logged_in')) {
    function is_admin_logged_in()
    {
        return session('sid') !== null;
    }
}

if (!function_exists('trxId')) {
    function trxId($randomLength = 6, $timeLength = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';
        for ($i = 0; $i < $randomLength; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        $timePortion = substr(time(), -$timeLength);

        return $randomString . $timePortion;
    }
}
function kyc_status($value = '')
{
    switch ($value) {
        case '1':
            $c = 'bg-success';
            $t = 'Verified';
            break;
        default:
            $c = 'bg-warning';
            $t = 'Unverified';
    }
    $xhtml = sprintf('<span class="badge text-dark %s">%s</span>', $c, $t);
    return $xhtml;
}

if (!function_exists("create_random_api_key")) {
    function create_random_string_key($length = 32, $type = '')
    {
        if ($type == 'number') {
            $characters = '0123456789';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}


if (!function_exists('_is_ajax')) {
    function _is_ajax(string $module_request = '')
    {
        $request = \Config\Services::request();
        $isAjax = $request->isAJAX();

        if (!$isAjax) {
            header('Location: ' . previous_url());
            die;
        }
    }
}
if (!function_exists('_is_permit')) {
    function _is_permit($permission, bool $return = false)
    {
        if (!array_key_exists($permission, PERMISSIONS) && current_admin('role_id') != 1) {
            if ($return) {
                $data['denied'] = "Yes";
                echo view('Admin\Views\no_permission', $data);
                die;
            }
            header('Location: ' . admin_url('permission-denied'));
            die;
        }
    }
}
if (!function_exists('makeUrlFriendly')) {
    function makeUrlFriendly($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/\s+/', '-', $text);
        $text = trim($text, '-');
        $text = urlencode($text);

        return $text;
    }
}

if (!function_exists('set_flashdata')) {
    function set_flashdata($key, $value)
    {
        $session = service('session');
        $session->setFlashdata($key, $value);
    }
}

if (!function_exists('throttler')) {
    function throttler($number = 6)
    {
        $throttler = \config\Services::throttler();
    }
}

if (!function_exists('get_flashdata')) {
    function get_flashdata($key)
    {
        $session = service('session');
        return $session->getFlashdata($key) ?? null;
    }
}

// File: app/Helpers/my_helper.php

if (!function_exists('post')) {
    function post($name = null, $char = false)
    {
        $request = service('request');

        if ($name !== null) {
            if ($char) {
                $rawInput = $request->getRawInput();
                $result = xss_clean($rawInput['message']) ?? null;
            } else {
                $post = $request->getPost($name);

                if (is_string($post)) {
                    $result = trim($post);
                    $result = addslashes($result);
                    $result = strip_tags($result);
                } else {
                    $result = $post;
                }
            }

            return $result;
        }
        return $request->getPost();
    }
}

function set_cookies($name, $value, $expire = 12000, $domain = '', $path = '/', $secure = false, $httponly = false, $samesite = 'Lax', $raw = false)
{
    // Load the cookie helper
    helper('cookie');

    // Define the cookie parameters
    $cookie = [
        'name'     => $name,
        'value'    => $value,
        'expire'   => $expire,
        'domain'   => $domain,
        'path'     => $path,
        'secure'   => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite,
        'raw'      => $raw,
    ];

    // Set the cookie
    set_cookie($cookie);
}
if (!function_exists('ids')) {
    function ids()
    {
        return md5(uniqid(mt_rand(), true));
    }
}

if (!function_exists('ip')) {

    function ip()
    {
        $request = service('request');
        return $request->getIPAddress();
    }
}


if (!function_exists("save_web_image")) {
    function save_web_image($url = "", $filename = '', $user = "")
    {
        if ($filename == '') {
            $filename = create_random_string_key(10) . '.jpg';
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $image_data = curl_exec($ch);
        curl_close($ch);



        if ($image_data === false) {
            return;
        }
        $destination_folder = get_upload_folder($user); // Replace with your desired folder path

        // Create the folder if it doesn't exist
        if (!is_dir($destination_folder)) {
            mkdir($destination_folder, 0755, true);
        }

        // Save the image
        $filepath = $destination_folder . $filename;
        file_put_contents($filepath, $image_data);

        // Check if the image was saved successfully
        if (file_exists($filepath)) {
            return get_link_file($filename, $user);
        }
        return;
    }
}



if (!function_exists('get_logo')) {
    function get_logo($site_icon = false): string
    {
        if ($site_icon) {
            $url = site_config('site_icon');
            if (!empty($url) && file_exists(ROOTPATH . $url)) {
                return base_url($url);
            }
            return base_url('public/assets/img/favicon.png');
        } else {
            $url = site_config('site_logo');
            if (!empty($url) && file_exists(ROOTPATH . $url)) {
                return base_url($url);
            }
            return base_url('public/assets/img/logo.png');
        }
    }
}
if (!function_exists('get_avatar')) {
    function get_avatar($type = 'admin', $id = '')
    {
        $db = db_connect();
        if ($type == "user") {
            $id = empty($id) ? session('uid') : $id;
            $query = $db->table('users')->select('avatar')->where('id', $id)->get();
            $user = $query->getRow();
            $url = $user ? $user->avatar : '';
        } elseif ($type == "admin") {
            $id = empty($id) ? session('sid') : $id;

            // Use the $db variable to execute queries
            $query = $db->table('staffs')->select('avatar')->where('id', $id)->get();
            $user = $query->getRow();
            $db->close();

            $url = $user ? $user->avatar : '';
        }

        if (!empty($url) && file_exists(ROOTPATH . $url)) {
            return base_url($url);
        }

        return base_url('public/assets/img/avatar.png');
    }
}
if (!function_exists('shorten_string')) {
    function shorten_string($input, $maxLength = 8)
    {
        if (mb_strlen($input) > $maxLength) {
            $lastSpacePosition = mb_strrpos(mb_substr($input, 0, $maxLength), ' ');
            if ($lastSpacePosition !== false) {
                return rtrim(mb_substr($input, 0, $lastSpacePosition)) . '...';
            }
            return mb_substr($input, 0, $maxLength) . '...';
        }
        return $input;
    }
}


if (!function_exists('segment')) {
    function segment($index)
    {
        $uri = new CodeIgniter\HTTP\URI(current_url());
        return $uri->getSegment($index);
    }
}

if (!function_exists('get_value')) {
    function get_value($dataJson, $key, $parseArray = false, $return = false)
    {
        if (is_string($dataJson)) {
            $dataJson = json_decode($dataJson);
        }

        if (is_object($dataJson)) {
            if (isset($dataJson->$key)) {
                if ($parseArray) {
                    return (array) $dataJson->$key;
                } else {
                    return $dataJson->$key;
                }
            }
        } elseif (is_array($dataJson)) {
            if (isset($dataJson[$key])) {
                return $dataJson[$key];
            }
        } else {
            return $dataJson;
        }

        return $return;
    }
}
// app/Helpers/TimeHelper.php

if (!function_exists('time_ago')) {
    function time_ago($datetime_str)
    {
        $datetime = Time::createFromFormat('Y-m-d H:i:s', $datetime_str);
        $now = Time::now();

        $diff = $now->getTimestamp() - $datetime->getTimestamp();

        $minute = 60;
        $hour = $minute * 60;
        $day = $hour * 24;
        $week = $day * 7;

        if ($diff < $minute) {
            return floor($diff) . ' seconds ago';
        } elseif ($diff < $hour) {
            $minutes = floor($diff / $minute);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($diff < $day) {
            $hours = floor($diff / $hour);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < $week) {
            $days = floor($diff / $day);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return $datetime->humanize();
        }
    }
}
if (!function_exists('arrayLike')) {
    function arrayLike($pattern, $array)
    {
        $matches = [];

        foreach ($array as $key => $value) {
            if (stripos($value, $pattern) !== false) {
                $matches[$key] = $value;
            }
        }

        return $matches;
    }
}

if (!function_exists('lan')) {
    function lan($slug)
    {
        $result = str_replace("_", " ", $slug);
        return mb_ucfirst(trim($result));
    }
}
function mb_ucfirst($str)
{
    return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));
}
if (!function_exists('_validation')) {
    function _validation($status, $ms)
    {
        ms(['status' => $status, 'message' => $ms]);
    }
}
function xss_clean($data)
{
    $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);

    // we are done...
    return $data;
}


if (!function_exists('now')) {
    function now()
    {
        return Time::now();
    }
}
if (!function_exists('trxId')) {
    function trxId()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomLength = 6; // Length of the random string
        $timeLength = 6; // Length of the time portion

        $randomString = '';
        for ($i = 0; $i < $randomLength; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        $timePortion = substr(time(), -$timeLength);

        return $randomString . $timePortion;
    }
}

if (!function_exists('get')) {
    function get($var)
    {
        $request = service('request');
        return $request->getGet($var);
    }
}




function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// URL-safe base64 decoding
function base64url_decode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

// Encode the values into a single parameter
function encodeParams($data = [])
{
    return base64url_encode(json_encode($data));
}

// Decode the parameter back into separate values
function decodeParams($param)
{
    $decoded = json_decode(base64url_decode($param), true);
    return $decoded;
}
function isEnglish($text)
{
    $pattern = '/^[\p{L}0-9\s.,:;!?()<>\/\-\[\]{}\'" ]+$/u';
    $pattern_exclude = '/{{.*?}}/';

    if (preg_match($pattern, $text) && !preg_match($pattern_exclude, $text)) {
        $max = get_option('sms_api_char_length', '160');
    } else {
        $max = get_option('sms_api_o_char_length', '60');
    }

    return $max;
}

if (!function_exists('calculateExpirationDate')) {
    function calculateExpirationDate($day, $expire = '')
    {
        $currentDate = new DateTime($expire);
        if (!$currentDate || $currentDate < new DateTime()) {
            $currentDate = new DateTime();
        }
        $currentDate->modify('+' . $day . ' days');
        $expirationDate = $currentDate->format('Y-m-d H:i:s');

        return $expirationDate;
    }
}
if (!function_exists('calculateMoneyExpenditure')) {
    function calculateExpenditure($expireDatetime, $createdDatetime, $price)
    {
        $expireTimestamp = strtotime($expireDatetime);
        $createdTimestamp = strtotime($createdDatetime);
        $currentTimestamp = time();

        // Calculate the total number of days between creation and expiration
        $totalDays = floor(($expireTimestamp - $createdTimestamp) / (60 * 60 * 24));

        // Calculate the number of days elapsed until today
        $elapsedDays = floor(($currentTimestamp - $createdTimestamp) / (60 * 60 * 24));

        // Calculate the expenditure
        $expendedAmount = ($price / $totalDays) * $elapsedDays;

        return $expendedAmount;
    }
}
if (!function_exists('hasExpired')) {
    function hasExpired($expirationDateTime)
    {
        $currentDateTime = new DateTime();
        $expirationDateTimeObj = new DateTime($expirationDateTime);

        return $currentDateTime > $expirationDateTimeObj;
    }
}



if (!function_exists("get_field")) {
    function get_field($table, $where = [], $field = '')
    {
        $model = new Model();
        $item  = $model->get($field, $table, $where);
        if (!empty($item) && isset($item->$field)) {
            return $item->$field;
        } else {
            return false;
        }
    }
}
if (!function_exists('show_item_transaction_type')) {
    function show_item_transaction_type($payment_method)
    {
        $xhtml = null;
        $params = get_field('payments', ['type' => $payment_method], "params");
        $image_payment_path = '';
        if ($params) {
            $image_payment_path = json_decode($params)->option->logo;
        }
        if (!empty($image_payment_path)) {
            $xhtml = sprintf('<img class="payment" src="%s" alt="%s" height="25">', base_url($image_payment_path), $payment_method);
        } else {
            $xhtml = sprintf('<span class="badge bg-success">%s</span>', ucfirst($payment_method));
        }
        return $xhtml;
    }
}

if (!function_exists('load_404')) {
    function load_404()
    {
        $response = \Config\Services::response();
        $response->setStatusCode(404);
        $output = view("errors/404");
        $response->setBody($output);
        $response->send();
        exit;
    }
}
