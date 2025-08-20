<?php

if (file_exists('../../../app/phpqrcode/vendor/autoload.php')) {
    require_once '../../../app/phpqrcode/vendor/autoload.php';
} elseif (file_exists('../../app/phpqrcode/vendor/autoload.php')) {
    require_once '../../app/phpqrcode/vendor/autoload.php';
} elseif (file_exists('../app/phpqrcode/vendor/autoload.php')) {
    require_once '../app/phpqrcode/vendor/autoload.php';
} else {
    require_once './app/phpqrcode/vendor/autoload.php';
}

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeGenerator
{
    public function generateQRCode($text)
    {
        $qr_code = QrCode::create($text)
            ->setSize(300)
            ->setMargin(20)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        $writer = new PngWriter;
        $result = $writer->write($qr_code);
        $writer = new PngWriter;
        $result = $writer->write($qr_code);
        $dataUri = $result->getDataUri();
        echo $dataUri;
    }
}
?>