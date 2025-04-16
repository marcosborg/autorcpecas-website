<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Image;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncStockImagesToTelepecas extends Command
{
    protected $signature = 'sync:stock-images-telepecas';
    protected $description = 'Sincroniza apenas a primeira imagem de cada stock com a API da Telepeças';

    public function handle()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            $this->error('❌ Token de autenticação não foi obtido.');
            return 1;
        }

        $images = Image::all();

        foreach ($images as $image) {
            $this->line("📦 Stock ID: {$image->stock_id}");
            $this->line("🖼️ Campo bruto: '{$image->images}'");

            if (empty(trim($image->images))) {
                $this->warn("⚠️ Campo images vazio para o stock {$image->stock_id}");
                continue;
            }

            $urls = collect(explode(',', $image->images))
                ->map(fn($url) => trim($url))
                ->filter(fn($url) => filter_var($url, FILTER_VALIDATE_URL))
                ->values()
                ->all();

            if (empty($urls)) {
                $this->warn("⚠️ Nenhuma imagem válida para o stock {$image->stock_id}");
                continue;
            }

            // Apenas a primeira imagem será enviada
            $firstUrl = $urls[0];

            $imagesPayload = [[
                'externalImageId' => (string) $image->stock_id,
                'urlImage' => $firstUrl,
                'isMaster' => "1"
            ]];

            $payload = [
                'token' => env('TELEPECAS_PUBLIC_KEY'),
                'externalId' => (int) $image->stock_id,
                'images' => $imagesPayload
            ];

            $this->line("📤 Payload: " . json_encode($payload, JSON_PRETTY_PRINT));

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.telepecas.com/stock/updateImages', $payload);

            if ($response->successful()) {
                $this->info("✔️ Primeira imagem do stock #{$image->stock_id} enviada com sucesso.");
            } else {
                $this->error("❌ Erro ao enviar imagem do stock #{$image->stock_id}:");
                $this->error("Status: " . $response->status());
                $this->error("Resposta: " . $response->body());
            }
        }

        return 0;
    }

    private function getAccessToken()
    {
        return Cache::remember('telepecas_token', 3600, function () {
            $response = Http::withHeaders([
                'Authorization' => env('TELEPECAS_AUTH'),
            ])->asForm()->post('https://api.telepecas.com/auth/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            return null;
        });
    }
}
