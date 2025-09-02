<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Сумма')
                    ->numeric()
                    ->prefix('₽')
                    ->required(),
                
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает',
                        'processing' => 'Обрабатывается',
                        'completed' => 'Завершен',
                        'failed' => 'Неудачный',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                    ])
                    ->required(),
                
                Forms\Components\Select::make('provider')
                    ->label('Провайдер')
                    ->options([
                        'yookassa' => 'YooKassa',
                        'cloudpayments' => 'CloudPayments',
                    ])
                    ->required(),
                
                Forms\Components\TextInput::make('provider_payment_id')
                    ->label('ID платежа у провайдера')
                    ->maxLength(255),
                
                Forms\Components\Select::make('subscription_id')
                    ->label('Подписка')
                    ->relationship('subscription', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "#{$record->id} - {$record->plan->name}"),
                
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(2),
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
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                        'primary' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'processing' => 'Обрабатывается',
                        'completed' => 'Завершен',
                        'failed' => 'Неудачный',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('provider')
                    ->label('Провайдер')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'yookassa' => 'YooKassa',
                        'cloudpayments' => 'CloudPayments',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('subscription.plan.name')
                    ->label('План')
                    ->sortable(),
                
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
                        'failed' => 'Неудачный',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                    ]),
                
                Tables\Filters\SelectFilter::make('provider')
                    ->label('Провайдер')
                    ->options([
                        'yookassa' => 'YooKassa',
                        'cloudpayments' => 'CloudPayments',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить платеж'),
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
