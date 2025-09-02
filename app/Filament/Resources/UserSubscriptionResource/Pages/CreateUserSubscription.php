<?php

namespace App\Filament\Resources\UserSubscriptionResource\Pages;

use App\Filament\Resources\UserSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserSubscription extends CreateRecord
{
    protected static string $resource = UserSubscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Устанавливаем статус по умолчанию
        $data['status'] = $data['status'] ?? 'pending';
        
        // Устанавливаем автопродление по умолчанию
        $data['auto_renewal'] = $data['auto_renewal'] ?? true;
        
        // Если не указана дата начала, устанавливаем текущую дату
        if (!isset($data['starts_at'])) {
            $data['starts_at'] = now();
        }
        
        // Если не указана дата окончания, рассчитываем на основе плана
        if (!isset($data['ends_at']) && isset($data['plan_id'])) {
            $plan = \App\Models\Plan::find($data['plan_id']);
            if ($plan) {
                $startsAt = \Carbon\Carbon::parse($data['starts_at']);
                $data['ends_at'] = match($plan->type) {
                    'monthly' => $startsAt->addMonth(),
                    'yearly' => $startsAt->addYear(),
                    'personal' => $startsAt->addMonth(), // По умолчанию месяц
                    default => $startsAt->addMonth(),
                };
            }
        }
        
        // Устанавливаем цену из плана если не указана
        if (!isset($data['price']) && isset($data['plan_id'])) {
            $plan = \App\Models\Plan::find($data['plan_id']);
            if ($plan) {
                $data['price'] = $plan->price;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
