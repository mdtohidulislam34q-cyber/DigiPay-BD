<?php
if (!function_exists('getEmailTemplate')) {
    function getEmailTemplate($key = "")
    {
        $result = (object) array();
        $result->subject = '';
        $result->content = '';
        if (!empty($key)) {
            switch ($key) {

                case 'user_plan':
                    $result->subject = "{{website_name}} -  Congratulations!";
                    $result->short_keys = json_encode([
                        'website_name' => 'website name',
                        'first_name' =>'User Firstname',
                        'last_name' =>'User Lastname',
                        'pay_amount'   => 'Transaction amount',
                        'plan'   => 'Plan Name',
                        'device'       => 'Number of device',
                        'website'       => 'Number of website',
                        'expire'         => 'Date of expire',
                        'date'         => 'Date',
                    ]);
                    $result->content = "Hi<strong> {{first_name}}!</strong>A plan of {{pay_amount}} tk has been added to your account successfully!</p>";
                    return $result;
                    break;

                case 'user_addon':
                    $result->subject = "{{website_name}} -  Congratulations!";
                    $result->short_keys = json_encode([
                        'website_name' => 'website name',
                        'first_name' =>'User Firstname',
                        'last_name' =>'User Lastname',
                        'pay_amount'   => 'Transaction amount',
                        'addon'       => 'Name of addon',
                        'date'         => 'Date',
                    ]);
                    $result->content = "Hi<strong> {{first_name}}!</strong>A {{addon}} of {{pay_amount}} tk has been added to your account successfully!</p>";
                    return $result;
                    break;

                case 'user_customer_trx':
                    $result->subject = "{{website_name}} -  Tansaction Success !";
                    $result->short_keys = json_encode([
                        'website_name' => 'website name',
                        'first_name' =>' User Firstname',
                        'last_name' =>' User Lastname',
                        'pay_amount'   => 'Transaction amount',
                        'trx_id'         => 'Transaction id',
                        'customer_name' =>'Your Customer Name',
                        'customer_email' =>'Your Customer Email',
                        'date'         => 'Date',
                    ]);
                    $result->content = "Hi<strong> {{customer_name}}!</strong>A payment of {{pay_amount}} tk has been successfully delivered to {{first_name}}!";
                    return $result;
                    break;
                case 'user_trx':
                    $result->subject = "{{website_name}} -  Tansaction Success !";
                    $result->short_keys = json_encode([
                        'website_name' => 'website name',
                        'first_name' =>' User Firstname',
                        'last_name' =>' User Lastname',
                        'pay_amount'   => 'Transaction amount',
                        'trx_id'         => 'Transaction id',
                        'customer_email' =>'Your Customer Email',
                        'customer_name' =>'Your Customer Name',
                        'date'         => 'Date',
                    ]);
                    $result->content = "Hi<strong> {{customer_name}}!</strong>A payment of {{pay_amount}} tk has been successfully delivered to {{first_name}}!";
                    return $result;
                    break;

                case 'payment':
                    $result->short_keys = json_encode([
                        'website_name'    => 'website name',
                        'pay_amount'      => 'Payment amount',
                        'first_name'      =>' User Firstname',
                        'last_name'       =>' User Lastname',
                        'email'           =>' User Email',
                        'balance'         => 'User Balance',
                        'date'            => 'Date',
                    ]);
                    $result->subject = "{{website_name}} -  Thank You! Deposit Payment Received";
                    $result->content = "<p>Hi<strong> {{first_name}}! </strong></p><p>We&#39;ve just received your final remittance and would like to thank you. We appreciate your diligence in adding funds to your balance in our service.</p><p>It has been a pleasure doing business with you. We wish you the best of luck.</p><p>Thanks and Best Regards!</p>";
                    return $result;
                    break;

                case 'verify':
                    $result->short_keys = json_encode([
                        'website_name'    => 'website name',
                        'activation_link' => 'Activation link',
                        'first_name'      =>' User Firstname',
                        'last_name'       =>' User Lastname',
                        'email'           =>' User Email',
                        'balance'         => 'User Balance',
                        'date'            => 'Date',
                    ]);
                    $result->subject = "{{website_name}} - Please validate your account";
                    $result->content = "<p><strong>Welcome to {{website_name}}! </strong></p><p>Hello <strong>{{first_name}}</strong>!</p><p> Thank you for joining! We&#39;re glad to have you as community member, and we&#39;re stocked for you to start exploring our service.  If you don&#39;t verify your address, you won&#39;t be able to create a User Account.</p><p>  All you need to do is activate your account by click this link: <br>  {{activation_link}} </p><p>Thanks and Best Regards!</p>";
                    return $result;
                    break;

                case 'welcome':
                    $result->short_keys = json_encode([
                        'website_name'=> 'website name',
                        'first_name'  =>' User Firstname',
                        'last_name'   =>' User Lastname',
                        'email'       =>' User Email',
                        'timezone'    =>' User Timezone',
                        'balance'     => 'User Balance',
                        'date'        => 'Date',
                    ]);
                    $result->subject = "{{website_name}} - Getting Started with Our Service!";
                    $result->content = "<p><strong>Welcome to {{website_name}}! </strong></p><p>Hello <strong>{{first_name}}</strong>!</p><p>Congratulations! <br>You have successfully signed up for our service - {{website_name}} with follow data</p><ul><li>Firstname: {{first_name}}</li><li>Lastname: {{last_name}}</li><li>Email: {{email}}</li><li>Timezone: {{user_timezone}}</li></ul><p>We want to exceed your expectations, so please do not hesitate to reach out at any time if you have any questions or concerns. We look to working with you.</p><p>Best Regards,</p>";
                    return $result;
                    break;

                case 'forgot_password':
                    $result->short_keys = json_encode([
                        'website_name'=> 'website name',
                        'first_name'  =>' User Firstname',
                        'last_name'   =>' User Lastname',
                        'email'       =>' User Email',
                        'timezone'    =>' User Timezone',
                        'recovery_password_link' => 'Recovery Pasword Link For User',
                        'balance'     => 'User Balance',
                        'date'        => 'Date',
                    ]);
                    $result->subject = "{{website_name}} - Password Recovery";
                    $result->content = "<p>Hi<strong> {{first_name}}! </strong></p><p>Somebody (hopefully you) requested a new password for your account. </p><p>No changes have been made to your account yet. <br>You can reset your password by click this link: <br>{{recovery_password_link}}</p><p>If you did not request a password reset, no further action is required. </p><p>Thanks and Best Regards!</p>                ";
                    return $result;
                    break;
                case 'admin_forgot_password':
                    $result->short_keys = json_encode([
                        'website_name'=> 'website name',
                        'first_name'  =>' User Firstname',
                        'last_name'   =>' User Lastname',
                        'email'       =>' User Email',
                        'balance'     => 'User Balance',
                        'admin_recovery_password_link' => 'Recovery Pasword Link For Admin',
                        'date'        => 'Date',
                    ]);
                    $result->subject = "{{website_name}} - Password Recovery";
                    $result->content = "<p>Hi<strong> {{first_name}}! </strong></p><p>Somebody (hopefully you) requested a new password for your account. </p><p>No changes have been made to your account yet. <br>You can reset your password by click this link: <br>{{admin_recovery_password_link}}</p><p>If you did not request a password reset, no further action is required. </p><p>Thanks and Best Regards!</p>                ";
                    return $result;
                    break;


                case 'new_user':
                    $result->short_keys = json_encode([
                        'website_name'=> 'website name',
                        'first_name'  =>' User Firstname',
                        'last_name'   =>' User Lastname',
                        'email'       =>' User Email',
                        'timezone'    =>' User Timezone',
                        'balance'     => 'User Balance',
                        'date'        => 'Date',
                    ]);
                    $result->subject = "{{website_name}} - New Registration";
                    $result->content = "<p>Hi Admin!</p><p>Someone signed up in <strong>{{website_name}}</strong> with follow data</p><ul><li>Firstname {{first_name}}</li><li>Lastname: {{last_name}}</li><li>Email: {{email}}</li><li>Timezone: {{user_timezone}}</li></ul> ";
                    return $result;
                    break;
                
                case 'user_message':
                    $result->subject    = "{{website_name}} Mail To User";
                    $result->short_keys = json_encode([
                        'website_name'=> 'website name',
                        'first_name'  =>' User Firstname',
                        'last_name'   =>' User Lastname',
                        'email'       =>' User Email',
                        'timezone'    =>' User Timezone',
                        'balance'     => 'User Balance',
                        'date'        => 'Date',
                    ]);
                    $result->content = "Hi<strong> {{first_name}}!</strong> of {{pay_amount}} tk has been successfully delivered to {{first_name}}!";
                    return $result;
                    break;


            }
        }
        return $result;
    }
}

