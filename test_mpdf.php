<?php
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

try {
    $mpdf = new Mpdf();
    echo "mPDF loaded successfully!";
} catch (\Mpdf\MpdfException $e) {
    die('Failed to initialize mPDF: ' . $e->getMessage());
}
