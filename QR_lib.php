<?php

namespace App\Libraries;

require 'Qr/vendor/autoload.php';

use Endroid\QrCode\QrCode;

class QR_lib {
    public $size,$padding,$logo_size;

    public function __construct($size=150,$logo_size=45,$padding=5)
    {
        $this->size      = $size;
        $this->logo_size = $logo_size;
        $this->padding   = $padding;

    }

    public function ccreate($text="Default Text")
    {
        // Path to the image file relative to the public directory
        $logoPath = site_config('site_icon');

        // Get the absolute path to the public directory
        $publicPath = realpath($logoPath);

        // Combine the public path with the relative image path

        $qrCode = new QrCode();
        $qrCode->setText($text)
            ->setSize($this->size)
            ->setPadding($this->padding)
            ->setErrorCorrection('high')
            ->setForegroundColor(['r' => 255, 'g' => 0, 'b' => 0]) // Red foreground color
            ->setBackgroundColor(['r' => 211, 'g' => 255, 'b' => 246]) // Custom background color
            ->setLogo($publicPath) // Path to the logo image
            ->setLogoSize($this->logo_size)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        
        // Send output of the QRCode directly
        header('Content-Type: '.$qrCode->getContentType());
        $qrCode->render();
    }

}
