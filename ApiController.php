<?php
namespace App\Controllers;

use Blocks\Models\QueueModel;

class ApiController extends BaseController
{
    protected $tb_file_manage;
    protected $controller_name;
    protected $image;
    public $model, $db;

    public function __construct()
    {
        $this->model = new QueueModel;
    }

    public function deviceConnect()
    {
        $this->db = db_connect();
        $request = service('request');

        $user_email = $request->getVar('user_email');
        $device_key = $request->getVar('device_key');
        $device_ip = $request->getVar('device_ip');

        if ($user_email && $device_key && $device_ip) {
            $uid = $this->model->get('id, uid, device_ip', 'devices', [
                'user_email' => $user_email, 
                'device_key' => $device_key
            ]);

            if ($uid) {
                if (deviceValidation($device_key, $uid->uid)) {
                    if (empty($uid->device_ip)) {
                        $data['device_ip'] = $device_ip;
                        $this->db->table('devices')->where('id', $uid->id)->update($data);
                        return json_encode(["status" => "1", "message" => $uid->uid]);
                    } elseif ($uid->device_ip === $device_ip) {
                        return json_encode(["status" => "1", "message" => $uid->uid]);
                    } else {
                        return json_encode(["status" => "3", "message" => 'Already connected with a different device']);
                    }
                } else {
                    return json_encode(["status" => "2", "message" => 'Your key is expired']);
                }
            }
        }

        return json_encode(['status' => '0', 'message' => 'Failed to connect with the server']);
    }
    
    public function addMessage()
    {
        $request = service('request');
        $deviceResponse = $this->deviceConnect();
        $device = json_decode($deviceResponse);

        // Handle JSON parsing errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            ms([
                'status' => 0,
                'message' => 'JSON Parsing Error: ' . json_last_error_msg(),
                'response' => $deviceResponse
            ]);
            return;
        }

        $address = $request->getVar('address');
        $message = $request->getVar('message');

        // Validate input
      //  if (empty($message) || !in_array($address, ['bkash', 'nagad', 'rocket'//, 'upay', 'surecash', 'Ipay', 'okwallet', 'tap', 'cellfin'])) {
          //  ms(["status" => "0", "message" => 'Invalid message or address']);
     //       return;
    //    }

        // Check device status and insert message
        if (!empty($device->status) && $device->status == 1 && !empty($message)) {
            $data = [
                'uid'        => $device->message,
                'message'    => preg_replace("/\r?\n/", " ", $message),
                'address'    => $address,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->table('module_data')->insert($data);

            if ($this->db->affectedRows() > 0) {
                ms(['status' => 1, 'message' => 'Data inserted successfully']);
            } else {
                ms(["status" => "0", "message" => 'Failed to insert data']);
            }
        } else {
            ms(['status' => "0", 'message' => 'Failed to connect or invalid device']);
        }
    }

    public function cron()
    {
        $this->model->processPendingTasks();
    }
}

?>
