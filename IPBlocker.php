<?php

namespace App\Filters;

use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class IPBlocker implements FilterInterface
{
    
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = Services::throttler();
        if($throttler->check($request->getIPAddress(), 35, MINUTE)===FALSE){
            ms(['status'=>'error','message'=>'Too many requests at same time']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
                
    }
}
