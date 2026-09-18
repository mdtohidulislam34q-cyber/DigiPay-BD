<?php

use Config\App_config;

if (!function_exists('get_option')) {
    function get_option(string $key, string $value = ''): string
    {
        // check data from $GLOBALS
        if (isset($GLOBALS['app_settings'][$key])) {
            return $GLOBALS['app_settings'][$key];
        }

        $db = db_connect();
        $result = $db->table('options')->select('value')->where('name', $key)->get()->getRow();

        if (empty($result)) {
            $db->table('options')->insert(['name' => $key, 'value' => $value]);
            return $value;
        } else {
            return $result->value;
        }
    }
}

if (!function_exists("update_option")) {
    function update_option($key, $value)
    {
        $db = db_connect();
        $result = $db->table('options')->select('value')->where('name', $key)->get()->getRow();

        if (empty($result)) {
            $db->table('options')->insert(['name' => $key, 'value' => $value]);
        } else {
            $db->table('options')->where('name', $key)->update(['value' => $value]);
        }
    }
}



if (!function_exists('app_config')) {
    function app_config($key)
    {
        return config('App_config')->$key;
    }
}

if (!function_exists("site_config")) {
    function site_config($key, $default = "")
    {
        $config = config('Site_config');
        
        // Check if the key exists in the loaded config
        if(isset($config->$key)){
            return $config->$key;
        }

        $configFilePath = APPPATH . 'Config/Site_config.php';

        // Load the existing configuration file
        require_once($configFilePath);

        // Add or update the key-value pair in the class
        $configData = new Config\Site_config();
        $configData->$key = $default;

        // Convert the class instance to a PHP string representation
        $configString = '<?php' . PHP_EOL . PHP_EOL;
        $configString .= 'namespace Config;' . PHP_EOL . PHP_EOL;
        $configString .= 'class Site_config extends \CodeIgniter\Config\BaseConfig' . PHP_EOL;
        $configString .= '{' . PHP_EOL;
        foreach ($configData as $property => $value) {
            $configString .= "\t" . 'public $' . $property . ' = ' . var_export($value, true) . ';' . PHP_EOL;
        }
        $configString .= '}' . PHP_EOL;

        // Write the updated configuration data back to the file
        file_put_contents($configFilePath, $configString);

        // Return the default value
        return $default;
    }
}

if (!function_exists("update_site_config")) {
    function update_site_config($key, $value)
    {
        $config = config('Site_config');
        
        // Update the key-value pair in the loaded config
        $config->$key = $value;

        // Get the path to the configuration file
        $configFilePath = APPPATH . 'Config/Site_config.php';

        // Convert the updated configuration data to a PHP string representation
        $configString = '<?php' . PHP_EOL . PHP_EOL;
        $configString .= 'namespace Config;' . PHP_EOL . PHP_EOL;
        $configString .= 'class Site_config extends \CodeIgniter\Config\BaseConfig' . PHP_EOL;
        $configString .= '{' . PHP_EOL;
        foreach ($config as $property => $value) {
            $configString .= "\t" . 'public $' . $property . ' = ' . var_export($value, true) . ';' . PHP_EOL;
        }
        $configString .= '}' . PHP_EOL;

        // Write the updated configuration data back to the file
        file_put_contents($configFilePath, $configString);

        // Return true to indicate successful update
        return true;
    }
}


function tz_list()
{
    $timezoneIdentifiers = DateTimeZone::listIdentifiers();
    $utcTime = new DateTime('now', new DateTimeZone('UTC'));

    $tempTimezones = array();
    foreach ($timezoneIdentifiers as $timezoneIdentifier) {
        $currentTimezone = new DateTimeZone($timezoneIdentifier);

        $tempTimezones[] = array(
            'offset' => (int) $currentTimezone->getOffset($utcTime),
            'identifier' => $timezoneIdentifier,
        );
    }

    // Sort the array by offset, identifier ascending
    usort($tempTimezones, function ($a, $b) {
        return ($a['offset'] == $b['offset'])
        ? strcmp($a['identifier'], $b['identifier'])
        : $a['offset'] - $b['offset'];
    });

    $timezoneList = array();
    foreach ($tempTimezones as $key => $tz) {
        $sign = ($tz['offset'] > 0) ? '+' : '-';
        $offset = gmdate('H:i', abs($tz['offset']));
        $timezoneList[$key]['time'] = '(UTC ' . $sign . $offset . ') ' . $tz['identifier'];
        $timezoneList[$key]['zone'] = $tz['identifier'];
    }
    return $timezoneList;
}
