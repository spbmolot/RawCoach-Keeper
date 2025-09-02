<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserSubscriptionResource\Pages;
use App\Filament\Resources\UserSubscriptionResource\RelationManagers;
use App\Models\UserSubscription;
use App\Models\Plan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserSubscriptionResource extends Resource
{
    protected static ?string $model = UserSubscription::class;
    
    protected static ?string $modelLabel = 'Подписка';
    protected static ?string $pluralModelLabel = 'Подписки';
    protected static ?string $navigationLabel = 'Подписки';
    protected static ?string $navigationGroup = 'Подписки и планы';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

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
                        
                        Forms\Components\Select::make('plan_id')
                            ->label('Тарифный план')
                            ->relationship('plan', 'name')
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активна',
                                'paused' => 'Приостановлена',
                                'cancelled' => 'Отменена',
                                'expired' => 'Истекла',
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Даты')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Дата начала')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Дата окончания')
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('cancelled_at')
                            ->label('Дата отмены'),
                        
                        Forms\Components\DateTimePicker::make('paused_at')
                            ->label('Дата приостановки'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Финансовая информация')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Цена')
                            ->numeric()
                            ->prefix('₽')
                            ->required(),
                        
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Размер скидки')
                            ->numeric()
                            ->prefix('₽')
                            ->default(0),
                        
                        Forms\Components\TextInput::make('coupon_code')
                            ->label('Код купона')
                            ->maxLength(50),
                    ])->columns(2),
                
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Причина отмены')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        Forms\Components\Toggle::make('auto_renewal')
                            ->label('Автопродление')
                            ->default(true),
                        
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('План')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'paused',
                        'danger' => 'cancelled',
                        'secondary' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Активна',
                        'paused' => 'Приостановлена',
                        'cancelled' => 'Отменена',
                        'expired' => 'Истекла',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Скидка')
                    ->money('RUB')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('coupon_code')
                    ->label('Купон')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Начало')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Окончание')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('auto_renewal')
                    ->label('Автопродление')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активна',
                        'paused' => 'Приостановлена',
                        'cancelled' => 'Отменена',
                        'expired' => 'Истекла',
                    ]),
                
                Tables\Filters\SelectFilter::make('plan')
                    ->label('План')
                    ->relationship('plan', 'name'),
                
                Tables\Filters\TernaryFilter::make('auto_renewal')
                    ->label('Автопродление'),
                
                Tables\Filters\Filter::make('expires_soon')
                    ->label('Истекают скоро')
                    ->query(fn (Builder $query): Builder => $query->where('ends_at', '<=', now()->addDays(7))),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Создана с'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Создана по'),
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
                Tables\Actions\Action::make('cancel')
                    ->label('Отменить')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (UserSubscription $record) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                        ]);
                    })
                    ->visible(fn (UserSubscription $record) => $record->status === 'active'),
                
                Tables\Actions\Action::make('pause')
                    ->label('Приостановить')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (UserSubscription $record) {
                        $record->update([
                            'status' => 'paused',
                            'paused_at' => now(),
                        ]);
                    })
                    ->visible(fn (UserSubscription $record) => $record->status === 'active'),
                
                Tables\Actions\Action::make('resume')
                    ->label('Возобновить')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (UserSubscription $record) {
                        $record->update([
                            'status' => 'active',
                            'paused_at' => null,
                        ]);
                    })
                    ->visible(fn (UserSubscription $record) => $record->status === 'paused'),
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
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserSubscriptions::route('/'),
            'create' => Pages\CreateUserSubscription::route('/create'),
            'view' => Pages\ViewUserSubscription::route('/{record}'),
            'edit' => Pages\EditUserSubscription::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            UserSubscriptionResource\Widgets\SubscriptionStatsOverview::class,
        ];
    }
}
