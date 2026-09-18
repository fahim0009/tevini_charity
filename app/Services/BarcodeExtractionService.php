<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class BarcodeExtractionService
{
    private $config;

    public function __construct()
    {
        // Auto-detect OS - works on both Windows (local) and Linux (server)
        if (PHP_OS_FAMILY === 'Windows') {
            $this->config = $this->getWindowsConfig();
        } else {
            $this->config = $this->getLinuxConfig();
        }
    }

    private function getWindowsConfig()
    {
        return [
            'ghostscript' => '"C:\\Program Files\\gs\\gs10.07.0\\bin\\gswin64c.exe"',
            'tesseract'   => 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'zbar'        => '"C:\\Program Files (x86)\\ZBar\\bin\\zbarimg.exe"',
        ];
    }

    private function getLinuxConfig()
    {
        return [
            'ghostscript' => 'gs',
            'tesseract'   => '/usr/bin/tesseract',
            'zbar'        => '/usr/bin/zbarimg',
        ];
    }

    /**
     * ✅ MAIN METHOD: Extract barcode from a single image
     * Uses priority chain (same as your PDF processing)
     */
    public function extractBarcodeFromImage($imagePath)
    {
        try {
            // Step 1: Validate image
            if (!$this->isValidImage($imagePath)) {
                Log::info("Image not valid: {$imagePath}");
                return null;
            }

            // Step 2: Check if it's actually a voucher
            if (!$this->isLikelyVoucher($imagePath, $this->config['tesseract'])) {
                Log::info("Not a voucher: {$imagePath}");
                return null;
            }

            // Priority 1: ZBar (reads QR code / barcode)
            $voucherNumber = $this->extractByZBar($imagePath, $this->config['zbar']);
            if ($voucherNumber) {
                Log::info("Found by ZBar: {$voucherNumber}");
                return $voucherNumber;
            }

            // Priority 2: OCR Top-Right Corner (cheque number location)
            $topRightPath = $this->cropTopRightCorner($imagePath);
            if ($topRightPath) {
                $voucherNumber = $this->extractByOCRSmart($topRightPath, $this->config['tesseract']);
                @unlink($topRightPath);
                if ($voucherNumber) {
                    Log::info("Found by OCR Top-Right: {$voucherNumber}");
                    return $voucherNumber;
                }
            }

            // Priority 3: Smart OCR (full image)
            $voucherNumber = $this->extractByOCRSmart($imagePath, $this->config['tesseract']);
            if ($voucherNumber) {
                Log::info("Found by OCR Smart: {$voucherNumber}");
                return $voucherNumber;
            }

            // Priority 4: Enhanced + ZBar
            $enhancedPath = $this->enhanceForBarcode($imagePath);
            if ($enhancedPath) {
                $voucherNumber = $this->extractByZBar($enhancedPath, $this->config['zbar']);
                @unlink($enhancedPath);
                if ($voucherNumber) return $voucherNumber;
            }

            // Priority 5: Cropped bottom + ZBar
            $croppedPath = $this->cropBottomArea($imagePath);
            if ($croppedPath) {
                $voucherNumber = $this->extractByZBar($croppedPath, $this->config['zbar']);
                @unlink($croppedPath);
                if ($voucherNumber) return $voucherNumber;
            }

            // Priority 6: Enhanced OCR
            $enhancedPath = $this->enhanceImageForOCR($imagePath);
            if ($enhancedPath) {
                $voucherNumber = $this->extractByOCRSmart($enhancedPath, $this->config['tesseract']);
                @unlink($enhancedPath);
                if ($voucherNumber) return $voucherNumber;
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Barcode extraction error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if image is actually a voucher
     */
    public function isLikelyVoucher($imagePath, $tesseractPath)
    {
        try {
            $text = (new TesseractOCR($imagePath))
                ->executable($tesseractPath)
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();

            $text = strtolower(trim($text));

            $voucherKeywords = [
                'voucher', 'cheque', 'check', 'amount', 'pounds', 'pay',
                'date', 'signature', 'valid', 'charity', 'donation',
                'receipt', 'payment', '£', '$', 'eur',
            ];

            $keywordCount = 0;
            foreach ($voucherKeywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $keywordCount++;
                }
            }

            if ($keywordCount >= 2) return true;

            $textLength = strlen(preg_replace('/\s+/', '', $text));
            if ($textLength < 20) return false;

            $lettersOnly = preg_replace('/[^a-zA-Z]/', '', $text);
            if (strlen($lettersOnly) < 5) return false;

            $structurePatterns = [
                '/no\.?\s*\d/i', '/date/i', '/amount/i', '/__+/',
                '/\d{2}\/\d{2}/', '/£\s*\d+/',
            ];

            $structureCount = 0;
            foreach ($structurePatterns as $pattern) {
                if (preg_match($pattern, $text)) {
                    $structureCount++;
                }
            }

            return ($structureCount >= 1 && $keywordCount >= 1);

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Smart OCR - prefers longer numbers (voucher over charity numbers)
     */
    public function extractByOCRSmart($imagePath, $tesseractPath)
    {
        try {
            $text = (new TesseractOCR($imagePath))
                ->executable($tesseractPath)
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();

            // Try specific "No." patterns
            $noPatterns = [
                '/No\.?\s*[:\-]?\s*(\d{8,12})/i',
                '/Number\s*[:\-]?\s*(\d{8,12})/i',
                '/Voucher\s*No\.?\s*[:\-]?\s*(\d{8,12})/i',
                '/Check\s*No\.?\s*[:\-]?\s*(\d{8,12})/i',
                '/Cheque\s*No\.?\s*[:\-]?\s*(\d{8,12})/i',
                '/Ref\s*[:\-]?\s*(\d{8,12})/i',
            ];

            foreach ($noPatterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    return $matches[1];
                }
            }

            // Find all numbers, return longest
            preg_match_all('/\b(\d{6,12})\b/', $text, $allNumbers);
            if (!empty($allNumbers[1])) {
                $numbers = $allNumbers[1];
                usort($numbers, function($a, $b) {
                    return strlen($b) - strlen($a);
                });
                return $numbers[0];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ZBar - reads QR codes and barcodes
     */
    public function extractByZBar($imagePath, $zbarPath)
    {
        try {
            $command = $zbarPath . ' --raw -q "' . $imagePath . '"';
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode === 0 && !empty($output)) {
                foreach ($output as $line) {
                    $line = trim($line);
                    if (preg_match('/^(\d{6,12})$/', $line)) {
                        return $line;
                    }
                    if (preg_match('/(\d{6,12})/', $line, $matches)) {
                        return $matches[1];
                    }
                }
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function cropTopRightCorner($imagePath)
    {
        try {
            $croppedPath = str_replace('.jpg', '_topright.jpg', $imagePath);
            $command = 'convert "' . $imagePath . '" -crop 40%x30%+60%+0 +repage "' . $croppedPath . '"';
            exec($command . ' 2>&1', $output, $returnCode);
            return ($returnCode === 0 && file_exists($croppedPath)) ? $croppedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function enhanceForBarcode($imagePath)
    {
        try {
            $enhancedPath = str_replace('.jpg', '_barenh.jpg', $imagePath);
            $command = 'convert "' . $imagePath . '" -colorspace GRAY -threshold 50% -scale 200% -scale 50% "' . $enhancedPath . '"';
            exec($command . ' 2>&1', $output, $returnCode);
            return ($returnCode === 0 && file_exists($enhancedPath)) ? $enhancedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function enhanceImageForOCR($imagePath)
    {
        try {
            $enhancedPath = str_replace('.jpg', '_enh.jpg', $imagePath);
            $command = 'convert "' . $imagePath . '" -colorspace GRAY -contrast-stretch 5%x5% -threshold 45% "' . $enhancedPath . '"';
            exec($command . ' 2>&1', $output, $returnCode);
            return ($returnCode === 0 && file_exists($enhancedPath)) ? $enhancedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function cropBottomArea($imagePath)
    {
        try {
            $croppedPath = str_replace('.jpg', '_crop.jpg', $imagePath);
            $command = 'convert "' . $imagePath . '" -gravity south -crop 100%x40%+0+0 +repage "' . $croppedPath . '"';
            exec($command . ' 2>&1', $output, $returnCode);
            return ($returnCode === 0 && file_exists($croppedPath)) ? $croppedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function isValidImage($imagePath)
    {
        if (!file_exists($imagePath)) return false;
        $info = @getimagesize($imagePath);
        if (!$info) return false;
        if ($info[0] < 100 || $info[1] < 100) return false;
        if (filesize($imagePath) < 5000) return false;
        return true;
    }
}