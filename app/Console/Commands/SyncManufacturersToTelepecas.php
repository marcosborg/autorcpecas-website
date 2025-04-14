<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncManufacturersToTelepecas extends Command
{
    protected $signature = 'sync:manufacturers-telepecas';
    protected $description = 'Sincroniza os manufacturers com a API da Telepeças';

    public function handle()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            $this->error('❌ Não foi possível obter o token de autenticação.');
            return 1;
        }

        $manufacturers = Manufacturer::all();

        foreach ($manufacturers as $manufacturer) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken, // token de autenticação
            ])->post('https://api.telepecas.com/catalog/makes/createMake', [
                'token' => env('TELEPECAS_PUBLIC_KEY'), // identificador do cliente
                'description' => $manufacturer->name,
                'externalId' => (string) $manufacturer->prestashop_manufacturer_id,
            ]);

            if ($response->successful()) {
                $this->info("✔️ {$manufacturer->name} sincronizado com sucesso.");
            } else {
                $this->error("❌ Erro ao sincronizar {$manufacturer->name}: " . $response->body());
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
