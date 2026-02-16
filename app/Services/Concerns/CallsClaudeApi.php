<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CallsClaudeApi
{
    private function callClaudeApi(string $systemPrompt, string $userMessage): ?string
    {
        $apiKey = config('services.anthropic.api_key');
        $model = config('services.anthropic.model');

        if (!$apiKey) {
            Log::error('Voice: ANTHROPIC_API_KEY not configured');
            return null;
        }

        try {
            $response = Http::timeout(15)->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Voice: Claude API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json('content.0.text');
        } catch (\Exception $e) {
            Log::error('Voice: Claude API exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function parseClaudeResponse(?string $responseText): ?array
    {
        if (!$responseText) {
            return null;
        }

        // Claude might wrap JSON in ```json blocks
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($responseText));

        $data = json_decode($cleaned, true);

        if (!$data || !isset($data['status'])) {
            Log::warning('Voice: Failed to parse Claude response', ['response' => $responseText]);
            return null;
        }

        return $data;
    }
}
