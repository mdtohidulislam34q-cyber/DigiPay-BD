<?php

namespace App\Libraries;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\I18n\Time;

class Smssender {

    protected $db;

    public function __construct() {
        $this->db = db_connect();
    }

    public function send_sms($templateKey, $params, $requestMessage = null, $number, $uid = null)
    {
        $permitted = false;
        $sms_data = [
            'type'   =>  1,
            'medium' => $number,
            'status' => 0,
            'created_at' => now()
        ];
        $charge = (double)get_option('sms_api_cost');

        if (is_null($uid)) {
            $requestMessage = get_option($templateKey);
            $permitted = true;

        } else {
            $templateObj = $this->CI->db->get_where('email_templates', ['uid' => $uid,'template_key'=>$templateKey])->row();
            if (empty($templateObj)) {
                return false;
            }

            $user = get_current_user_data($uid);

            $bal = (double)$user->balance ;

            $sms_data['uid'] = $uid;
            if (get_value($user->addons,'sms') !=1) {
                return false;
            }

            if ($templateObj->sms_status!=1) {
                return false;
            }

        }
        
        if (empty($templateObj) && $requestMessage == null) {
            return false;
        }

        $template = !empty($templateObj) ? $templateObj->sms_body : $requestMessage;

        foreach ($params as $code => $value) {
            $template = str_replace('{{' . $code . '}}', $value, $template);
        }

        $sms_data['message'] = $template;
        $template = strip_tags($template);

        $max = isEnglish($template);

        $length = strlen($template);
        $counter = ceil($length/$max);
        $charge = $charge*$counter;

        $sms_data['charge'] = $charge;

        if (!empty($uid)) {
            if ($bal > $charge) {
                $permitted = true;
            }else{
                $sms_data['charge'] = '0';
                $sms_data['status'] = '3';
                $sms_data['response'] = 'Low Balance';
            }
        }


        $this->CI->db->insert('user_notifier',$sms_data);
        $insert_id = $this->CI->db->insert_id();

        if (!$permitted) {
            return false;
        }

        if (!empty($uid)) {
            $this->CI->db->set('balance', "balance-$charge", FALSE);
            $this->CI->db->where('id', $uid);
            $this->CI->db->update(USERS);
            if ($this->CI->db->affected_rows() <= 0) {return false;}
        }

        $paramData = is_null(get_option('sms_api_params')) ? [] : json_decode(get_option('sms_api_params'), true);
        $paramData = http_build_query($paramData);
        $actionUrl = get_option('sms_api_url');
        $actionMethod = get_option('sms_api_method');
        $formData = is_null(get_option('sms_api_formdata')) ? [] : json_decode(get_option('sms_api_formdata'), true); 

        $headerData = is_null(get_option('sms_api_header_data')) ? [] : json_decode(get_option('sms_api_header_data'), true);
        if ($actionMethod == 'GET') {
            $actionUrl = $actionUrl . '?' . $paramData;
        }

        $formData = $this->recursive_array_replace("[[receiver]]", $number, $this->recursive_array_replace("[[message]]", $template, $formData));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $actionUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $actionMethod,
            CURLOPT_POSTFIELDS => http_build_query($formData),
            CURLOPT_HTTPHEADER => $headerData,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        if (get_value($response,get_option('sms_api_success_key')) != get_option('sms_api_success_value')) {
            $data['response'] = $response;
            $data['status'] = 2;
            if (!empty($uid)) {
                $this->CI->db->set('balance', "balance+$charge", FALSE);
                $this->CI->db->where('id', $uid);
                $this->CI->db->update(USERS);
                if ($this->CI->db->affected_rows() <= 0) {return false;}
            }
        }else{
            $data['response'] = $response;
            $data['status'] = 1;

        }
        $this->CI->db->update('user_notifier',$data,['id'=>$insert_id]);

        return $response;
    }

   public function recursive_array_replace($search, $replace, $array) {
        if (!is_array($array)) {
            return str_replace($search, $replace, $array);
        }
        foreach ($array as $key => $value) {
            $array[$key] = $this->recursive_array_replace($search, $replace, $value);
        }
        return $array;
    }

}
