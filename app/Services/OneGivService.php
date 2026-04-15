<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\OneGiv\OneGivCardOrder;

class OneGivService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('onegiv.base_url') 
                        ?? 'https://cardissuerapitest.onegiv.co.uk';
        $this->token   = config('onegiv.token') ?? '';
    }

    private function client()
    {
        return Http::withToken($this->token)
                   ->baseUrl($this->baseUrl)
                   ->acceptJson()
                   ->asJson();
    }

    /**
     * Order cards from OneGiv
     */
    public function orderCards(array $cards): array
    {
        $response = $this->client()->post('/order-cards', $cards);

        if ($response->failed()) {
            Log::error('OneGiv orderCards failed', ['body' => $response->body()]);
            throw new \Exception('OneGiv API Error: ' . $response->body());
        }

        $result = $response->json();

        foreach ($cards as $card) {
            OneGivCardOrder::create([
                'user_id'      => auth()->id(),
                'order_id'     => $card['id'],
                'order_number' => $result['orderNumber'] ?? null,
                'card_holder'  => $card['cardHolder'],
                'fixed_amount' => $card['fixedAmount'],
                'amount'       => $card['amount'],
                'pin'          => $card['pin'],
                'status'       => 'pending',
            ]);
        }

        return $result;
    }

    /**
     * Change PIN for a card
     */
    public function changePin(string $cardSerialNumber, string $newPin): array
    {
        $response = $this->client()->post('/change-pin', [
            'cardSerialNumber' => $cardSerialNumber,
            'pin'              => $newPin,
        ]);

        if ($response->failed()) {
            Log::error('OneGiv changePin failed', ['body' => $response->body()]);
            throw new \Exception('OneGiv PIN Change Error: ' . $response->body());
        }

        return $response->json();
    }

    // ------------------------------------------------
    // TEST ONLY 
    // ------------------------------------------------

    public function listCards(): array
    {
        $response = $this->client()->get('/list-cards');
        if ($response->failed()) throw new \Exception($response->body());
        return $response->json();
    }

    public function listCardOrders(): array
    {
        $response = $this->client()->get('/list-card-orders');
        if ($response->failed()) throw new \Exception($response->body());
        return $response->json();
    }

    public function simulateCardOrderProcessed(string $orderNumber): array
    {
        $response = $this->client()->get('/simulate-card-order-processed-callback', [
            'orderNumber' => $orderNumber,
        ]);
        if ($response->failed()) throw new \Exception($response->body());
        return $response->json();
    }
}