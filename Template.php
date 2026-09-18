<?php

namespace App\Libraries;

use CodeIgniter\View\Exceptions\ViewException;

class Template
{
    private $data = [];
    private $theme = '';
    private $layout = 'template';
    private $parserEnabled = true;
    private $themePath='';

    public function __construct()
    {
    }

    public function set($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
        $this->themePath = ROOTPATH . 'themes/' . $this->theme . '/';
        return $this;
    }

    public function set_layout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function enableParser($enable = true)
    {
        $this->parserEnabled = $enable;
        return $this;
    }

    public function view($view, $data = [])
    {
        // Use the loadModuleView method to set the 'view' key in $this->data
        $this->data['view'] = $this->loadModuleView(null, $view, $data);

        // Merge additional data with $this->data
        $this->data = array_merge($this->data, $data);

        return $this;
    }
        


    private function loadModuleView($module, $view, $data)
    {
        if ($module === null) {
            $module = $this->detectModuleName();
        }

        // Build the view path directly using the provided $view
        $viewPath = "Modules/".ucfirst($module) . '/Views/' . $view;
        $viewPath1 = ucfirst($module) . '\Views\\' . $view;

        // Check if the view file exists
        if (!file_exists(APPPATH . $viewPath . '.php')) {
            throw new \RuntimeException("View file '$view' not found in module '$module'.");
        }

        return $viewPath1;
    }



    private function detectModuleName()
    {
        // Get the current controller's class name
        $controllerClass = get_class($this->getControllerInstance());

        // Extract the module name from the controller's class name
        $matches = [];
        if (preg_match('/(?:Modules\\\\)?(.*?)\\\\Controllers/', $controllerClass, $matches)) {
            // If there is a match, return the captured group (content within parentheses)
            return $matches[1];
        }

        throw new \RuntimeException('Unable to detect module name.');
    }

    private function getControllerInstance()
    {
        $router = \Config\Services::router();
        $controller = $router->controllerName();

        // Output the controller name for debugging
        if (class_exists($controller)) {
            return new $controller();
        }

        throw new \RuntimeException('Unable to determine the current controller instance.');
    }
        
    
  


    public function render($options=[])
    {
        $this->data['template'] = [
            'theme' => $this->theme,
            'layout' => $this->layout,
        ];
    
        try {            
            $options['saveData'] = true;
            $output = view($this->themePath . 'layouts/' . $this->layout, $this->data, $options);

        } catch (ViewException $e) {
            throw $e; // Handle view exceptions appropriately
        }
    
        echo $output;
    }
    

}
