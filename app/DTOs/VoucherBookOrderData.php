<?php

namespace App\DTOs;

class VoucherBookOrderData
{
    public function __construct(
        public int    $donorId,            
        public int    $orderedByUserId,    
        public string $source,             
        public array  $items,              
        public bool   $isDelivery,
        public bool   $isCollection,
        public ?float $deliveryChargeOverride = null, 
    ) {}

    /**
     * Admin এর জন্য factory (voucherIds + qtys arrays থেকে)
     */
    public static function fromAdminRequest(
        int    $donorId,
        int    $adminId,
        array  $voucherIds,
        array  $qtys,
        bool   $isDelivery,
        bool   $isCollection,
        ?float $deliveryCharge = null
    ): self {
        $items = collect($voucherIds)
            ->map(fn($id, $key) => [
                'voucher_id' => (int)$id,
                'qty'        => (int)$qtys[$key],
            ])
            ->values()
            ->toArray();

        return new self(
            donorId:              $donorId,
            orderedByUserId:      $adminId,
            source:               'admin',
            items:                $items,
            isDelivery:           $isDelivery,
            isCollection:         $isCollection,
            deliveryChargeOverride: $deliveryCharge,
        );
    }

    /**
     * Donor (Web/App) এর জন্য factory (JSON vouchers string থেকে)
     */
    public static function fromDonorRequest(
        int    $donorId,
        string $vouchersJson,
        bool   $isDelivery,
        bool   $isCollection
    ): self {
        $decoded = json_decode($vouchersJson, true) ?? [];

        $items = collect($decoded)
            ->map(fn($item) => [
                'voucher_id' => (int)($item['voucherIds'] ?? $item['voucher_id'] ?? 0),
                'qty'        => (int)($item['qtys']        ?? $item['qty']        ?? 0),
            ])
            ->values()
            ->toArray();

        return new self(
            donorId:         $donorId,
            orderedByUserId: $donorId,
            source:          'app',  
            items:           $items,
            isDelivery:      $isDelivery,
            isCollection:    $isCollection,
        );
    }

    public function isForMixedOnly(): bool
    {
        return false;
    }
}