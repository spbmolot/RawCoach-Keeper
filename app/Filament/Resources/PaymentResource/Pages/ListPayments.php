<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

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
            
            'pending' => Tab::make('Ожидают')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge($this->getModel()::where('status', 'pending')->count()),
            
            'succeeded' => Tab::make('Успешные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'succeeded'))
                ->badge($this->getModel()::where('status', 'succeeded')->count()),
            
            'failed' => Tab::make('Неудачные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed'))
                ->badge($this->getModel()::where('status', 'failed')->count()),
            
            'refunded' => Tab::make('Возвращенные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'refunded'))
                ->badge($this->getModel()::where('status', 'refunded')->count()),
            
            'today' => Tab::make('Сегодня')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge($this->getModel()::whereDate('created_at', today())->count()),
        ];
    }
}
