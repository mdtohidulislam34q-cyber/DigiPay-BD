<?php

namespace App\Controllers;

use App\Libraries\Template;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;
    protected $template;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public $url;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->template = new Template();

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $this->url = $protocol . '://' . $_SERVER['HTTP_HOST'];

        if (empty($_SERVER['HTTPS']) && site_config('enable_https') == "1") {
            force_https();
        }

        if (getenv('app.baseURL') != $this->url && empty(segment(1))) {
            // $this->editEnv();
        }

        if (getenv('APP_STATUS') != 'installed') {
            header('Location: ' . $this->url . '/installer');
            exit;
        }
        $cookie_verify_maintenance_mode = "non-verified";
        if (get_option('is_maintenance_mode')) {
        }
        if (isset($_COOKIE["verify_maintenance_mode"]) && $_COOKIE["verify_maintenance_mode"] != "") {
            $cookie_verify_maintenance_mode = encrypt_decode($_COOKIE["verify_maintenance_mode"]);
        }
        if ($cookie_verify_maintenance_mode != 'verified' && get_option('is_maintenance_mode') && segment(1) != ("maintenance" || "cron")) {
            header('Location: ' . base_url('maintenance'));
            exit;
        }
        if (get_option('is_maintenance_mode') != 1 && segment(1) == "maintenance") {
            header('Location: ' . base_url('/'));
            exit;
        }
        
    }
    public function editEnv()
    {
        $envFilePath = ROOTPATH . '.env';
        $currentEnvContent = file_get_contents($envFilePath);
        $position = strpos($currentEnvContent, 'app.baseURL');

        if ($position !== false) {
            $lineEnd = strpos($currentEnvContent, PHP_EOL, $position);
            $currentLine = substr($currentEnvContent, $position, $lineEnd - $position);
            $newLine = str_replace(getenv('app.baseURL'), $this->url, $currentLine);
            $currentEnvContent = str_replace($currentLine, $newLine, $currentEnvContent);
        }

        file_put_contents($envFilePath, $currentEnvContent);
    }
}
