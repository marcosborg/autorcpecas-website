<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carmodel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncCarmodelsToTelepecas extends Command
{
    protected $signature = 'sync:carmodels-telepecas';
    protected $description = 'Sincroniza os modelos de carros com a API da Telepeças';

    public function handle()
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            $this->error('❌ Não foi possível obter o token de autenticação.');
            return 1;
        }

        $carmodels = Carmodel::all();

        foreach ($carmodels as $model) {
            $payload = [
                'token' => env('TELEPECAS_PUBLIC_KEY'),
                'externalId' => $model->externalId,
                'description' => $model->description,
                'externalMakeId' => $model->externalMakeId,
                'vehicleType' => $model->vehicleType,
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.telepecas.com/catalog/models/createModel', $payload);

            if ($response->successful()) {
                $this->info("✔️ Modelo '{$model->description}' enviado com sucesso.");
            } else {
                $this->error("❌ Erro ao enviar '{$model->description}':");
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
