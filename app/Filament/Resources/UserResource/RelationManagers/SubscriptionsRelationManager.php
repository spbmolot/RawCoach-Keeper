<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $title = 'Подписки';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plan_id')
                    ->label('План')
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
                
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Дата начала')
                    ->required(),
                
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('Дата окончания')
                    ->required(),
                
                Forms\Components\DateTimePicker::make('next_billing_date')
                    ->label('Следующий платеж'),
                
                Forms\Components\TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->prefix('₽')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('plan.name')
            ->columns([
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('План')
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
                
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Начало')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Окончание')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('next_billing_date')
                    ->label('Следующий платеж')
                    ->dateTime()
                    ->sortable(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить подписку'),
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
