<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiImageRecognitionService
{
    /**
     * Send image bytes to the Python AI service and return the predicted menu name.
     *
     * @param  string  $imagePath  Local path or temporary file path of the image.
     * @param  string  $filename  Original name of the file.
     *
     * @throws Exception
     */
    public function recognize(string $imagePath, string $filename): array
    {
        $url = config('services.ai.url').'/predict';

        try {
            $fileContent = Storage::disk('public')->get($imagePath);

            $response = Http::timeout(5)
                ->attach('image', $fileContent, $filename)
                ->post($url);

            if ($response->successful()) {
                $data = $response->json();

                // Log successful prediction
                Log::info('AI Image Recognition Success', [
                    'filename' => $filename,
                    'prediction' => $data['prediction'] ?? null,
                    'confidence' => $data['confidence'] ?? null,
                    'method' => $data['method'] ?? null,
                ]);

                return [
                    'success' => true,
                    'prediction' => $data['prediction'] ?? null,
                    'confidence' => $data['confidence'] ?? null,
                    'method' => $data['method'] ?? null,
                ];
            }

            Log::error('AI Image Recognition API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new Exception('Layanan AI mengembalikan status error: '.$response->status());
        } catch (Exception $e) {
            Log::error('AI Image Recognition Connection Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new Exception('Gagal terhubung dengan layanan AI. Pastikan layanan aktif.');
        }
    }
}
