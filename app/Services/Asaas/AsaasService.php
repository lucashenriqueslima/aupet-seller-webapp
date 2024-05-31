<?php

namespace App\Services\Asaas;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AsaasService
{
    private PendingRequest $client;
    public array $asaassCustomer = [];

    public function __construct()
    {
        $this->client = Http::withHeaders([
            'access_token' => "$" . env('ASAAS_API_KEY') . "==",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->baseUrl(env('ASAAS_API_URL'));
    }

    public function findOrCreateCustomer(array $customer): void
    {

        $this->getCustomerByCpf($customer['cpfCnpj']);

        if (!empty($this->asaassCustomer)) {
            return;
        }

        $this->createCustomer($customer);
    }

    public function createSignature(array $signature): void
    {
        try {
            $response = $this->client->post("/v3/subscriptions", $signature);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    private function createCustomer(array $customer): void
    {

        try {
            $response = $this->client->post("/v3/customers", $customer);

            $this->asaassCustomer = $response->json();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    private function getCustomerByCpf(string $cpf): void
    {
        try {
            $response = $this->client->get("/v3/customers", [
                'cpfCnpj' => $cpf,
            ]);

            if (empty($response->json()['data'])) {
                return;
            }

            $this->asaassCustomer = $response->json()['data'][0];
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
