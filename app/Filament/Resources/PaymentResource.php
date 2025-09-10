<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    
    protected static ?string $modelLabel = 'Платеж';
    protected static ?string $pluralModelLabel = 'Платежи';
    protected static ?string $navigationLabel = 'Платежи';
    protected static ?string $navigationGroup = 'Платежи';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('subscription_id')
                            ->label('Подписка')
                            ->relationship('subscription', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "#{$record->id} - {$record->plan->name}")
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('external_id')
                            ->label('Внешний ID')
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('provider')
                            ->label('Платежный провайдер')
                            ->options([
                                'yookassa' => 'YooKassa',
                                'cloudpayments' => 'CloudPayments',
                                'manual' => 'Ручной ввод',
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Финансовая информация')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Сумма')
                            ->numeric()
                            ->prefix('₽')
                            ->required(),
                        
                        Forms\Components\TextInput::make('currency')
                            ->label('Валюта')
                            ->default('RUB')
                            ->maxLength(3),
                        
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'pending' => 'Ожидает оплаты',
                                'paid' => 'Оплачен',
                                'failed' => 'Неудачно',
                                'cancelled' => 'Отменен',
                                'refunded' => 'Возвращен',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('payment_url')
                            ->label('URL оплаты')
                            ->maxLength(2048)
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Даты')
                    ->schema([
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Дата оплаты'),
                        
                        Forms\Components\DateTimePicker::make('failed_at')
                            ->label('Дата неудачи'),
                        
                        Forms\Components\DateTimePicker::make('refunded_at')
                            ->label('Дата возврата'),

                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Дата обработки'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(2)
                            ->maxLength(500),
                        
                        Forms\Components\Textarea::make('failure_reason')
                            ->label('Причина неудачи')
                            ->rows(2)
                            ->maxLength(500),
                        
                        Forms\Components\KeyValue::make('payload')
                            ->label('Данные провайдера (payload)')
                            ->keyLabel('Параметр')
                            ->valueLabel('Значение'),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Дополнительные данные')
                            ->keyLabel('Параметр')
                            ->valueLabel('Значение'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('subscription.plan.name')
                    ->label('План')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Сумма')
                    ->money('RUB')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ожидает',
                        'paid' => 'Оплачен',
                        'failed' => 'Неудачно',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('provider')
                    ->label('Провайдер')
                    ->colors([
                        'primary' => 'yookassa',
                        'success' => 'cloudpayments',
                        'secondary' => 'manual',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'yookassa' => 'YooKassa',
                        'cloudpayments' => 'CloudPayments',
                        'manual' => 'Ручной',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('external_id')
                    ->label('Внешний ID')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('payment_url')
                    ->label('URL оплаты')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Оплачен')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачен',
                        'failed' => 'Неудачно',
                        'cancelled' => 'Отменен',
                        'refunded' => 'Возвращен',
                    ]),
                
                Tables\Filters\SelectFilter::make('provider')
                    ->label('Провайдер')
                    ->options([
                        'yookassa' => 'YooKassa',
                        'cloudpayments' => 'CloudPayments',
                        'manual' => 'Ручной ввод',
                    ]),
                
                Tables\Filters\Filter::make('amount')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('Сумма от')
                            ->numeric(),
                        Forms\Components\TextInput::make('amount_to')
                            ->label('Сумма до')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Создан с'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Создан по'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('refund')
                    ->label('Вернуть')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        // Здесь должна быть логика возврата через API провайдера
                        $record->update([
                            'status' => 'refunded',
                            'refunded_at' => now(),
                        ]);
                    })
                    ->visible(fn (Payment $record) => $record->status === 'paid'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PaymentResource\Widgets\PaymentStatsOverview::class,
        ];
    }
}
