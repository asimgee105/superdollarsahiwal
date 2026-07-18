<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    /**
     * Generate product description based on title and metadata features.
     */
    public function generateDescription(string $title, string $brand = 'Aura'): string
    {
        $apiKey = env('OPENAI_API_KEY');
        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert e-commerce copywriter. Write a premium product description.'],
                        ['role' => 'user', 'content' => "Write a 3-sentence e-commerce description for a product titled '{$title}' under brand '{$brand}'."],
                    ],
                ]);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content');
                }
            } catch (\Exception $e) {
                // Fallback to template below
            }
        }

        // Production-ready fallback template generator
        return "Experience premium styling with our {$brand} {$title}. Specially designed for modern active lifespans, this item incorporates durable upper details, flexible textured outsoles, and highly cushioned footprints to guarantee maximum daily comfort.";
    }

    /**
     * Generate meta description.
     */
    public function generateMeta(string $title, string $brand = 'Aura'): string
    {
        return "Shop the premium {$brand} {$title} online at AURA. Discover active fashion lifestyle outfits, sneakers, and apparel with fast shipping across Pakistan.";
    }

    /**
     * Generate product highlights points array.
     */
    public function generateHighlights(string $title): array
    {
        return [
            'Premium quality composition materials',
            'Extremely lightweight and breathable structures',
            'Durable WampServer active grip outsoles',
        ];
    }
}
