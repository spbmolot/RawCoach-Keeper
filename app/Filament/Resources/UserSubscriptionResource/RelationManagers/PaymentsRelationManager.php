<?php

namespace App\Filament\Resources\UserSubscriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Платежи';

    protected static ?string $modelLabel = 'Платеж';

    protected static ?string $pluralModelLabel = 'Платежи';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Сумма')
                    ->numeric()
                    ->required(),
                    
                Forms\Components\Select::make('currency')
                    ->label('Валюта')
                    ->options([
                        'RUB' => 'Рубли',
                        'USD' => 'Доллары',
                        'EUR' => 'Евро',
                    ])
                    ->default('RUB')
                    ->required(),
                    
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'processing' => 'Обрабатывается',
                        'completed' => 'Завершен',
                        'failed' => 'Ошибка',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                    ])
                    ->required(),
                    
                Forms\Components\Select::make('provider')
                    ->label('Провайдер')
                    ->options([
                        'yookassa' => 'ЮKassa',
                        'cloudpayments' => 'CloudPayments',
                        'manual' => 'Ручной',
                    ])
                    ->required(),
                    
                Forms\Components\TextInput::make('provider_payment_id')
                    ->label('ID платежа провайдера')
                    ->maxLength(255),
                    
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(500),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('RUB')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('currency')
                    ->label('Валюта')
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('provider')
                    ->label('Провайдер')
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('provider_payment_id')
                    ->label('ID провайдера')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'processing' => 'Обрабатывается',
                        'completed' => 'Завершен',
                        'failed' => 'Ошибка',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                    ]),
                    
                Tables\Filters\SelectFilter::make('provider')
                    ->label('Провайдер')
                    ->options([
                        'yookassa' => 'ЮKassa',
                        'cloudpayments' => 'CloudPayments',
                        'manual' => 'Ручной',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
