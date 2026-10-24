<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeService
{
    /**
     * Generate SVG Data URI for a tracking URL
     */
    public static function generateTrackingQrDataUri(string $trackingReference): string
    {
        $trackingUrl = route('customer.track', ['ref' => $trackingReference]);

        $options = new QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
            'version' => 5,
            'eccLevel' => QRCode::ECC_M,
            'addQuietzone' => true,
        ]);

        $qrcode = new QRCode($options);

        return $qrcode->render($trackingUrl);
    }

    /**
     * Generate raw SVG string
     */
    public static function generateTrackingQrSvg(string $trackingReference): string
    {
        $dataUri = self::generateTrackingQrDataUri($trackingReference);
        if (str_starts_with($dataUri, 'data:image/svg+xml;base64,')) {
            return base64_decode(substr($dataUri, strlen('data:image/svg+xml;base64,')));
        }
        return $dataUri;
    }
}
