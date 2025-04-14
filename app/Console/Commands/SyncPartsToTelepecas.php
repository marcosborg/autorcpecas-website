<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Part;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncPartsToTelepecas extends Command
{
    protected $signature = 'sync:parts-telepecas';
    protected $description = 'Envia as partes da base de dados para a API da Telepeças';

    public function handle()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            $this->error('❌ Token de autenticação não foi obtido.');
            return 1;
        }

        $parts = Part::all();

        foreach ($parts as $part) {
            $payload = [
                'token' => env('TELEPECAS_PUBLIC_KEY'),
                'externalId' => $part->externalId,
                'partDescription' => [
                    [
                        'languageCode' => 'PT',
                        'content' => $part->name,
                    ]
                ],
                'partFixedObs' => [
                    [
                        'languageCode' => 'PT',
                        'content' => $part->name,
                    ]
                ],
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.telepecas.com/catalog/parts/createPart', $payload);

            if ($response->successful()) {
                $this->info("✔️ Parte '{$part->name}' enviada com sucesso.");
            } else {
                $this->error("❌ Erro ao enviar '{$part->name}':");
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
