<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Устанавливаем статус по умолчанию
        $data['status'] = $data['status'] ?? 'pending';
        
        // Устанавливаем валюту по умолчанию
        $data['currency'] = $data['currency'] ?? 'RUB';
        
        // Устанавливаем провайдер по умолчанию
        $data['provider'] = $data['provider'] ?? config('payments.default_provider', 'yookassa');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
