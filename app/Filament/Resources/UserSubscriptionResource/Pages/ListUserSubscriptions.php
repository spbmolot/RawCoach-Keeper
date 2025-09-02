<?php

namespace App\Filament\Resources\UserSubscriptionResource\Pages;

use App\Filament\Resources\UserSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUserSubscriptions extends ListRecords
{
    protected static string $resource = UserSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Все')
                ->badge($this->getModel()::count()),
            
            'active' => Tab::make('Активные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge($this->getModel()::where('status', 'active')->count()),
            
            'pending' => Tab::make('Ожидающие')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge($this->getModel()::where('status', 'pending')->count()),
            
            'cancelled' => Tab::make('Отмененные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge($this->getModel()::where('status', 'cancelled')->count()),
            
            'expired' => Tab::make('Истекшие')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'expired'))
                ->badge($this->getModel()::where('status', 'expired')->count()),
            
            'expires_soon' => Tab::make('Истекают скоро')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', 'active')
                    ->where('ends_at', '<=', now()->addDays(7))
                    ->where('ends_at', '>', now())
                )
                ->badge($this->getModel()::where('status', 'active')
                    ->where('ends_at', '<=', now()->addDays(7))
                    ->where('ends_at', '>', now())
                    ->count()),
        ];
    }
}
