<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfMergeService
{
    public function mergeFromUrls(array $urls): string
    {
        $pdf = new Fpdi();

        foreach ($urls as $index => $url) {
            $pdfContent = $this->downloadPdf($url);
            if (!$pdfContent) {
                continue;
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'pdf_merge_');
            file_put_contents($tempFile, $pdfContent);

            $pageCount = $pdf->setSourceFile($tempFile);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            unlink($tempFile);
        }

        return $pdf->Output('S', 'combined_labels.pdf');
    }

    /**
     * 🔥 Merge PDFs from base64 content (already resized) into a single PDF
     */
    public function mergeFromBase64(array $base64Contents, float $widthMm = 100, float $heightMm = 150): string
    {
        $pdf = new Fpdi();
        $orientation = ($widthMm > $heightMm) ? 'L' : 'P';

        foreach ($base64Contents as $base64) {
            $pdfContent = base64_decode($base64);
            if (!$pdfContent) {
                continue;
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'pdf_merge_');
            file_put_contents($tempFile, $pdfContent);

            $pageCount = $pdf->setSourceFile($tempFile);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $pdf->AddPage($orientation, [$widthMm, $heightMm]);
                $pdf->useTemplate($templateId);
            }

            unlink($tempFile);
        }

        return $pdf->Output('S', 'combined_labels.pdf');
    }

    private function downloadPdf(string $url): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['Accept' => 'application/pdf'])
                ->get($url);

            if ($response->successful()) {
                $body = $response->toPsrResponse()->getBody();
                $body->rewind();
                return $body->getContents();
            }

            Log::warning('PDF download failed for merge', [
                'url' => $url,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PDF download error during merge: ' . $e->getMessage());
            return null;
        }
    }
}
