<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\Barcode;
use App\Models\ProcessedBarcode;
use App\Jobs\ProcessBarcodeJob;

class ProcessVoucherController extends Controller
{
    
    private function getWindowsConfig()
    {
        return [
            'ghostscript' => '"C:\\Program Files\\gs\\gs10.07.0\\bin\\gswin64c.exe"',
            'tesseract' => 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'zbar' => '"C:\\Program Files (x86)\\ZBar\\bin\\zbarimg.exe"',
        ];
    }

    private function getLinuxConfig()
    {
        return [
            'ghostscript' => 'gs',
            'tesseract' => '/usr/bin/tesseract',
            'zbar' => '/usr/bin/zbarimg',
        ];
    }

    // ==========================================
    // WINDOWS VERSION
    // ==========================================
    
    public function uploadAndExtractMultiplepdf2(Request $request)
    {
        try {
            set_time_limit(7200);
            
            $config = $this->getWindowsConfig();

            $request->validate([
                'pdfFiles.*' => 'required|mimes:pdf|max:120000'
            ]);

            $allBarcodes = [];

            foreach ($request->file('pdfFiles') as $pdfFile) {
                $pdfPath = $pdfFile->store('public/pdfs');
                $pdfFullPath = storage_path('app/' . $pdfPath);

                $barcodeImagePath = storage_path('app/public/barcodeimages/');
                if (!file_exists($barcodeImagePath)) {
                    mkdir($barcodeImagePath, 0777, true);
                }

                Log::info('Starting: ' . $pdfFile->getClientOriginalName());

                $images = $this->convertPdfToImages($pdfFullPath, $barcodeImagePath, $config['ghostscript']);
                Log::info('Converted: ' . count($images) . ' images');

                $barcodes = $this->extractBarcodesFromImages($images, $config);
                $this->processVoucher($barcodes);

                $found = count(array_filter($barcodes, fn($b) => !in_array($b['voucher_number'], ['Not Found', 'Image unreadable'])));
                
                $allBarcodes[] = [
                    'pdf' => $pdfFile->getClientOriginalName(),
                    'total_pages' => count($images),
                    'found' => $found,
                    'not_found' => count($images) - $found,
                    'barcodes' => $barcodes
                ];
            }

            return response()->json([
                'message' => 'Extraction completed',
                'allBarcodes' => $allBarcodes
            ]);

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // LINUX VERSION
    // ==========================================

    public function uploadAndExtractMultiplepdf(Request $request)
    {
        try {
            set_time_limit(7200);
            $config = $this->getLinuxConfig();

            $request->validate([
                'pdfFiles.*' => 'required|mimes:pdf|max:120000'
            ]);

            $allBarcodes = [];

            foreach ($request->file('pdfFiles') as $pdfFile) {
                $pdfPath = $pdfFile->store('public/pdfs');
                $pdfFullPath = storage_path('app/' . $pdfPath);

                $barcodeImagePath = storage_path('app/public/barcodeimages/');
                if (!file_exists($barcodeImagePath)) {
                    mkdir($barcodeImagePath, 0777, true);
                }

                $images = $this->convertPdfToImages($pdfFullPath, $barcodeImagePath, $config['ghostscript']);
                $barcodes = $this->extractBarcodesFromImages($images, $config);
                $this->processVoucher($barcodes);

                $found = count(array_filter($barcodes, fn($b) => !in_array($b['voucher_number'], ['Not Found', 'Image unreadable'])));

                $allBarcodes[] = [
                    'pdf' => $pdfFile->getClientOriginalName(),
                    'total_pages' => count($images),
                    'found' => $found,
                    'not_found' => count($images) - $found,
                    'barcodes' => $barcodes
                ];
            }

            return response()->json([
                'message' => 'Extraction completed',
                'allBarcodes' => $allBarcodes
            ]);

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // SHARED FUNCTIONS
    // ==========================================

    private function convertPdfToImages($pdfPath, $outputDir, $ghostscriptPath)
    {
        $prefix = 'page_' . time() . '_';
        $outputPath = $outputDir . $prefix . '%d.jpg';

        $command = $ghostscriptPath . 
            ' -dNOPAUSE -dBATCH -dSAFER -sDEVICE=jpeg' .
            ' -dTextAlphaBits=4 -dGraphicsAlphaBits=4' .
            ' -r200 -dJPEGQ=90' .
            ' -sOutputFile="' . $outputPath . '"' .
            ' "' . $pdfPath . '"';

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Ghostscript failed: ' . implode("\n", $output));
        }

        return glob($outputDir . $prefix . '*.jpg') ?: [];
    }

    /**
     * ✅ EXTRACTION ORDER with VOUCHER VALIDATION
     */
    private function extractBarcodesFromImages($images, $config)
    {
        $barcodes = [];
        $total = count($images);

        foreach ($images as $index => $imagePath) {
            $current = $index + 1;
            Log::info("Processing {$current}/{$total}");

            if (!$this->isValidImage($imagePath)) {
                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => 'Image unreadable',
                    'method' => 'none'
                ];
                DB::table('processed_barcodes')->insert([
                    'file' => basename($imagePath),
                    'barcode' => 'Image unreadable',
                    'status' => 'Unreadable',
                    'method' => 'none',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                continue;
            }

            // ============================================
            // NEW: Check if this is actually a voucher
            // ============================================
            if (!$this->isLikelyVoucher($imagePath, $config['tesseract'])) {
                Log::info("  → Skipped: Not a voucher page");
                $barcodes[] = [
                    'file' => basename($imagePath),
                    'voucher_number' => 'Not Found',
                    'method' => 'skipped'
                ];
                // Don't insert into database - it's not a voucher
                continue;
            }

            $voucherNumber = null;
            $method = 'none';

            // ============================================
            // PRIORITY 1: ZBar Barcode
            // ============================================
            $voucherNumber = $this->extractByZBar($imagePath, $config['zbar']);
            if ($voucherNumber) {
                $method = 'ZBar';
                Log::info("  → Found by ZBar: {$voucherNumber}");
            }

            // ============================================
            // PRIORITY 2: OCR Top-Right Corner
            // ============================================
            if (!$voucherNumber) {
                $topRightPath = $this->cropTopRightCorner($imagePath);
                if ($topRightPath) {
                    $voucherNumber = $this->extractByOCRSmart($topRightPath, $config['tesseract']);
                    if ($voucherNumber) {
                        $method = 'OCR Top-Right';
                        Log::info("  → Found by OCR Top-Right: {$voucherNumber}");
                    }
                    @unlink($topRightPath);
                }
            }

            // ============================================
            // PRIORITY 3: Smart OCR (full image)
            // ============================================
            if (!$voucherNumber) {
                $voucherNumber = $this->extractByOCRSmart($imagePath, $config['tesseract']);
                if ($voucherNumber) {
                    $method = 'OCR Smart';
                    Log::info("  → Found by OCR Smart: {$voucherNumber}");
                }
            }

            // ============================================
            // PRIORITY 4: Enhanced + ZBar
            // ============================================
            if (!$voucherNumber) {
                $enhancedPath = $this->enhanceForBarcode($imagePath);
                if ($enhancedPath) {
                    $voucherNumber = $this->extractByZBar($enhancedPath, $config['zbar']);
                    if ($voucherNumber) {
                        $method = 'ZBar Enhanced';
                        Log::info("  → Found by ZBar Enhanced: {$voucherNumber}");
                    }
                    @unlink($enhancedPath);
                }
            }

            // ============================================
            // PRIORITY 5: Cropped Bottom + ZBar
            // ============================================
            if (!$voucherNumber) {
                $croppedPath = $this->cropBottomArea($imagePath);
                if ($croppedPath) {
                    $voucherNumber = $this->extractByZBar($croppedPath, $config['zbar']);
                    if ($voucherNumber) {
                        $method = 'ZBar Cropped';
                        Log::info("  → Found by ZBar Cropped: {$voucherNumber}");
                    }
                    @unlink($croppedPath);
                }
            }

            // ============================================
            // PRIORITY 6: Enhanced OCR
            // ============================================
            if (!$voucherNumber) {
                $enhancedPath = $this->enhanceImageForOCR($imagePath);
                if ($enhancedPath) {
                    $voucherNumber = $this->extractByOCRSmart($enhancedPath, $config['tesseract']);
                    if ($voucherNumber) {
                        $method = 'OCR Enhanced';
                        Log::info("  → Found by OCR Enhanced: {$voucherNumber}");
                    }
                    @unlink($enhancedPath);
                }
            }

            $barcodes[] = [
                'file' => basename($imagePath),
                'voucher_number' => $voucherNumber ?? 'Not Found',
                'method' => $method
            ];
        }

        return $barcodes;
    }

    /**
     * ✅ Check if image is ACTUALLY a voucher (not just random numbers)
     * Returns TRUE if it looks like a real voucher
     */
    private function isLikelyVoucher($imagePath, $tesseractPath)
    {
        try {
            $text = (new TesseractOCR($imagePath))
                ->executable($tesseractPath)
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();

            // Normalize text
            $text = strtolower(trim($text));
            
            // ============================================
            // CHECK 1: Must have voucher-related keywords
            // ============================================
            $voucherKeywords = [
                'voucher',
                'cheque',
                'check',
                'amount',
                'pounds',
                'pay',
                'date',
                'signature',
                'valid',
                'charity',
                'donation',
                'receipt',
                'payment',
                '£',  // Pound symbol
                '$',  // Dollar symbol
                'eur', // Euro
            ];

            $keywordCount = 0;
            foreach ($voucherKeywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $keywordCount++;
                }
            }

            // Must have at least 2 keywords to be a voucher
            if ($keywordCount >= 2) {
                return true;
            }

            // ============================================
            // CHECK 2: Must have enough text content
            // A real voucher has more than just a few numbers
            // ============================================
            $textLength = strlen(preg_replace('/\s+/', '', $text));
            
            // If text is very short (< 20 chars), probably not a voucher
            if ($textLength < 20) {
                return false;
            }

            // ============================================
            // CHECK 3: Must have text that's NOT just numbers
            // ============================================
            $lettersOnly = preg_replace('/[^a-zA-Z]/', '', $text);
            
            // If no letters at all, probably just a number page
            if (strlen($lettersOnly) < 5) {
                return false;
            }

            // ============================================
            // CHECK 4: Check for structured elements
            // ============================================
            $structurePatterns = [
                '/no\.?\s*\d/i',           // "No. 123456"
                '/date/i',                  // "Date"
                '/amount/i',                // "Amount"
                '/__+/',                    // Blank lines "___"
                '/\d{2}\/\d{2}/',         // Date format "01/01"
                '/£\s*\d+/',              // "£36.00"
            ];

            $structureCount = 0;
            foreach ($structurePatterns as $pattern) {
                if (preg_match($pattern, $text)) {
                    $structureCount++;
                }
            }

            // Must have at least 1 structural element
            if ($structureCount >= 1 && $keywordCount >= 1) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            // If OCR fails, assume it's not a voucher
            return false;
        }
    }




    /**
     * ✅ SMART OCR - Prefers longer numbers (8+ digits) over short (6-7 digits)
     * This avoids picking up charity numbers like 282079
     */
    private function extractByOCRSmart($imagePath, $tesseractPath)
    {
        try {
            $text = (new TesseractOCR($imagePath))
                ->executable($tesseractPath)
                ->lang('eng')
                ->psm(6)
                ->oem(1)
                ->run();

            // ============================================
            // STEP 1: Try specific "No." patterns (8+ digits preferred)
            // ============================================
            $noPatterns = [
                '/No\.?\s*[:\-]?\s*(\d{8,12})/i',           // No. 60292729
                '/Number\s*[:\-]?\s*(\d{8,12})/i',          // Number 60292729
                '/Voucher\s*No\.?\s*[:\-]?\s*(\d{8,12})/i', // Voucher No. 60292729
                '/Check\s*No\.?\s*[:\-]?\s*(\d{8,12})/i',   // Check No. 60292729
                '/Cheque\s*No\.?\s*[:\-]?\s*(\d{8,12})/i',  // Cheque No. 60292729
                '/Ref\s*[:\-]?\s*(\d{8,12})/i',             // Ref 60292729
            ];

            foreach ($noPatterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    return $matches[1];
                }
            }

            // ============================================
            // STEP 2: Find ALL numbers and pick the LONGEST one
            // ============================================
            preg_match_all('/\b(\d{6,12})\b/', $text, $allNumbers);
            
            if (!empty($allNumbers[1])) {
                // Sort by length (longest first)
                $numbers = $allNumbers[1];
                usort($numbers, function($a, $b) {
                    return strlen($b) - strlen($a);
                });

                // Return the longest number (likely the voucher number)
                // Voucher: 60292729 (8 digits) vs Charity: 282079 (6 digits)
                return $numbers[0];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ✅ Extract using ZBar (reads actual barcode)
     */
    private function extractByZBar($imagePath, $zbarPath)
    {
        try {
            $command = $zbarPath . ' --raw -q "' . $imagePath . '"';
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode === 0 && !empty($output)) {
                foreach ($output as $line) {
                    $line = trim($line);
                    // Accept 6-12 digit numbers from barcode
                    if (preg_match('/^(\d{6,12})$/', $line)) {
                        return $line;
                    }
                    // Extract digits if mixed with other chars
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

    /**
     * ✅ Crop TOP-RIGHT corner (where voucher number usually is)
     */
    private function cropTopRightCorner($imagePath)
    {
        try {
            $croppedPath = str_replace('.jpg', '_topright.jpg', $imagePath);
            
            // Crop top 30% and right 40%
            $command = 'magick "' . $imagePath . '" -crop 40%x30%+60%+0 +repage "' . $croppedPath . '"';
            
            exec($command . ' 2>&1', $output, $returnCode);

            return ($returnCode === 0 && file_exists($croppedPath)) ? $croppedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ✅ Enhance image specifically for barcode reading
     */
    private function enhanceForBarcode($imagePath)
    {
        try {
            $enhancedPath = str_replace('.jpg', '_barenh.jpg', $imagePath);
            
            // High contrast black & white for barcode
            $command = 'magick "' . $imagePath . '" -colorspace GRAY -threshold 50% -scale 200% -scale 50% "' . $enhancedPath . '"';
            
            exec($command . ' 2>&1', $output, $returnCode);

            return ($returnCode === 0 && file_exists($enhancedPath)) ? $enhancedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ✅ Enhance image for OCR
     */
    private function enhanceImageForOCR($imagePath)
    {
        try {
            $enhancedPath = str_replace('.jpg', '_enh.jpg', $imagePath);
            
            $command = 'magick "' . $imagePath . '" -colorspace GRAY -contrast-stretch 5%x5% -threshold 45% "' . $enhancedPath . '"';
            
            exec($command . ' 2>&1', $output, $returnCode);

            return ($returnCode === 0 && file_exists($enhancedPath)) ? $enhancedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ✅ Crop bottom area (where barcode is)
     */
    private function cropBottomArea($imagePath)
    {
        try {
            $croppedPath = str_replace('.jpg', '_crop.jpg', $imagePath);
            
            $command = 'magick "' . $imagePath . '" -gravity south -crop 100%x40%+0+0 +repage "' . $croppedPath . '"';
            
            exec($command . ' 2>&1', $output, $returnCode);

            return ($returnCode === 0 && file_exists($croppedPath)) ? $croppedPath : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ✅ Validate image
     */
    private function isValidImage($imagePath)
    {
        if (!file_exists($imagePath)) return false;
        $info = @getimagesize($imagePath);
        if (!$info) return false;
        if ($info[0] < 100 || $info[1] < 100) return false;
        if (filesize($imagePath) < 5000) return false;
        return true;
    }

    /**
     * ✅ Process and store results
     */
    private function processVoucher($barcodes)
    {
        $voucherNumbers = array_filter(
            array_unique(array_column($barcodes, 'voucher_number')),
            fn($v) => !in_array($v, ['Not Found', 'Image unreadable'])
        );

        $existing = DB::table('barcodes')
            ->whereIn('barcode', $voucherNumbers)
            ->pluck('barcode')
            ->toArray();

        foreach ($barcodes as $barcode) {
            $status = 'Not Found';
            if ($barcode['voucher_number'] === 'Image unreadable') $status = 'Unreadable';
            elseif (in_array($barcode['voucher_number'], $existing)) $status = 'Found';

            DB::table('processed_barcodes')->insert([
                'file' => $barcode['file'],
                'barcode' => $barcode['voucher_number'],
                'status' => $status,
                'method' => $barcode['method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ==========================================
    // QUEUE VERSION
    // ==========================================

    public function uploadAndExtract_queue(Request $request)
    {
        try {
            $request->validate([
                'pdfFile' => 'required|mimes:pdf|max:120000'
            ]);

            $pdfPath = $request->file('pdfFile')->store('public/pdfs');
            $pdfFullPath = storage_path('app/' . $pdfPath);
            $barcodeImagePath = storage_path('app/public/barcodeimages/');

            if (!file_exists($barcodeImagePath)) {
                mkdir($barcodeImagePath, 0777, true);
            }

            $config = $this->getWindowsConfig();

            ProcessBarcodeJob::dispatch(
                $pdfFullPath,
                $barcodeImagePath,
                $config
            )->onQueue('barcode_processing');

            return response()->json([
                'message' => 'File uploaded! Processing in background...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // OTHER METHODS (unchanged)
    // ==========================================

    public function addToProcessBarcode(Request $request)
    {
        $processBarcode = ProcessedBarcode::where('barcode', '!=', 'Not Found')->get();

        $prop = '';
        $prop2 = '';
        $orderDetails = [];
        
        foreach ($processBarcode as $pdata) {
            $orderDtl = Barcode::where('barcode', '=', $pdata->barcode)->first();

            if ($orderDtl) {
                $orderDetails[] = $orderDtl;
                $prop .= '<tr class="item-row">
                    <td width="200px">
                        <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px;" onclick="removeRow(event)">X</div>
                    </td>
                    <td width="200px">
                        <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="'.$orderDtl->user->accountno.'" placeholder="Type Acc no...">
                    </td>
                    <td width="250px">
                        <input style="min-width:100px" type="text" value="'.$orderDtl->user->name.'" readonly class="form-control donorAcc">
                        <input type="hidden" name="donor[]" value="'.$orderDtl->user_id.'" class="donorid">
                    </td>
                    <td width="250px">
                        <input style="min-width:100px" name="check[]" type="text" value="'.$pdata->barcode.'" class="form-control check">
                    </td>
                    <td width="20px">
                        <input style="min-width:30px" name="amount[]" type="text" value="'.$orderDtl->amount.'" class="amount form-control">
                    </td>
                    <td width="250px">
                        <input style="min-width:200px" name="note[]" type="text" class="form-control note">
                    </td>
                    <td width="150px">
                        <select name="waiting[]" class="form-control">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </td>
                </tr>';
            } else {
                $prop2 .= '<tr class="item-row">
                    <td width="200px" style="display:inline-flex;">
                        <div style="color: white; user-select:none; padding: 5px; background: red; width: 45px; display: flex; align-items: center; margin-right:5px; justify-content: center; border-radius: 4px;" onclick="removeRow(event)">X</div>
                    </td>
                    <td width="200px">
                        <input style="min-width: 100px;" type="number" class="form-control donor" name="donor_acc[]" value="" placeholder="Type Acc no...">
                    </td>
                    <td width="250px">
                        <input style="min-width:100px" type="text" value="" readonly class="form-control donorAcc">
                        <input type="hidden" name="donor[]" value="" class="donorid">
                    </td>
                    <td width="250px">
                        <input style="min-width:100px" name="check[]" type="text" value="'.$pdata->barcode.'" class="form-control check">
                    </td>
                    <td width="20px">
                        <input style="min-width:30px" name="amount[]" type="text" value="" class="amount form-control">
                    </td>
                    <td width="250px">
                        <input style="min-width:200px" name="note[]" type="text" class="form-control note">
                    </td>
                    <td width="150px">
                        <select name="waiting[]" class="form-control">
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </td>
                </tr>';
            }
        }

        return response()->json([
            'status' => 300,
            'data' => $prop,
            'data2' => $prop2,
            'orderDetails' => $orderDetails
        ], 200);
    }

    public function deleteProcessBarcode(Request $request)
    {
        try {
            $toDelete = ProcessedBarcode::where('barcode', '!=', 'Not Found')->get();
            
            foreach ($toDelete as $item) {
                $imagePath = storage_path('app/public/barcodeimages/' . $item->file);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            ProcessedBarcode::where('barcode', '!=', 'Not Found')->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Processed barcodes deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProcessBarcodeImage(Request $request)
    {
        try {
            $toDelete = ProcessedBarcode::where('barcode', '=', 'Not Found')->get();
            
            foreach ($toDelete as $item) {
                $imagePath = storage_path('app/public/barcodeimages/' . $item->file);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            ProcessedBarcode::where('barcode', '=', 'Not Found')->delete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Processed barcodes Image deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProcessSingleBarcode(Request $request)
    {
        try {
            $toDelete = ProcessedBarcode::where('id', '=', $request->id)->get();
            
            foreach ($toDelete as $item) {
                $imagePath = storage_path('app/public/barcodeimages/' . $item->file);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            ProcessedBarcode::where('id', '=', $request->id)->delete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Processed barcode Image deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}