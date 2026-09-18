<?php

namespace Config;

use Admin\Filters\Admin_auth;
use App\Filters\Auth;
use App\Filters\IPBlocker;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use User\Filters\User_auth;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => Auth::class,
        'ipblocker'     => IPBlocker::class,
        'user_auth'     => User_auth::class,
        'admin_auth'    => Admin_auth::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        'before' => [
            'honeypot',
            'csrf' => [
                'except' => [
                    'user/add_funds/complete/*',
                    'api/*',
                    'invoice/*',
                    'get_total_notifiaction_count',
                    'get_user_notifications',
                ],
            ],
        ],
        'after' => [
            'toolbar',
            'honeypot',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     */
    public array $filters = [];
}