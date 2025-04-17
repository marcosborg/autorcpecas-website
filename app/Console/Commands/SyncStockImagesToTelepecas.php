<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Image;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncStockImagesToTelepecas extends Command
{
    protected $signature = 'sync:stock-images-telepecas';
    protected $description = 'Sincroniza TODAS as imagens de TODOS os stocks com a API da Telepeças';

    public function handle()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            $this->error('❌ Token de autenticação não foi obtido.');
            return 1;
        }

        $images = Image::whereNotNull('images')->where('images', '!=', '')->where('id', '>', 5000)->get();

        foreach ($images as $image) {
            $this->line("📦 Stock ID: {$image->stock_id}");
            $this->line("🖼️ Campo bruto: '{$image->images}'");

            $urls = collect(explode(',', $image->images))
                ->map(fn($url) => trim($url))
                ->filter(fn($url) => filter_var($url, FILTER_VALIDATE_URL))
                ->values()
                ->all();

            if (empty($urls)) {
                $this->warn("⚠️ Nenhuma imagem válida para o stock {$image->stock_id}");
                continue;
            }

            $imagesPayload = [];
            foreach ($urls as $index => $url) {
                $imagesPayload[] = [
                    'externalImageId' => (string) $image->stock_id . '_' . $index,
                    'urlImage' => $url,
                    'isMaster' => $index === 0 ? "1" : "0"
                ];
            }

            $payload = [
                'token' => env('TELEPECAS_PUBLIC_KEY'),
                'externalId' => (string) $image->stock_id,
                'images' => $imagesPayload
            ];

            $this->line("📤 Enviando imagens do stock {$image->stock_id}...");
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.telepecas.com/stock/updateImages', $payload);

            if ($response->successful()) {
                $this->info("✔️ Imagens do stock #{$image->stock_id} enviadas com sucesso.");
            } else {
                $this->error("❌ Erro ao enviar imagens do stock #{$image->stock_id}:");
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

            return $response->successful()
                ? $response->json()['access_token']
                : null;
        });
    }
}
