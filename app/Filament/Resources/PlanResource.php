<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    
    protected static ?string $modelLabel = 'Тарифный план';
    protected static ?string $pluralModelLabel = 'Тарифные планы';
    protected static ?string $navigationLabel = 'Тарифные планы';
    protected static ?string $navigationGroup = 'Подписки';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        Forms\Components\Select::make('type')
                            ->label('Тип плана')
                            ->options([
                                'monthly' => 'Месячный',
                                'yearly' => 'Годовой',
                                'personal' => 'Персональный',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('Цена (руб.)')
                            ->numeric()
                            ->required()
                            ->prefix('₽'),
                        
                        Forms\Components\TextInput::make('duration_days')
                            ->label('Длительность (дни)')
                            ->numeric()
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Настройки доступа')
                    ->schema([
                        Forms\Components\Toggle::make('has_archive_access')
                            ->label('Доступ к архиву'),
                        
                        Forms\Components\Toggle::make('has_early_access')
                            ->label('Ранний доступ'),
                        
                        Forms\Components\Toggle::make('has_personal_plan')
                            ->label('Персональные планы'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\KeyValue::make('features')
                            ->label('Особенности плана')
                            ->keyLabel('Особенность')
                            ->valueLabel('Описание'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->colors([
                        'primary' => 'monthly',
                        'success' => 'yearly',
                        'warning' => 'personal',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Месячный',
                        'yearly' => 'Годовой',
                        'personal' => 'Персональный',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Дней')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Подписок')
                    ->counts('subscriptions'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип плана')
                    ->options([
                        'monthly' => 'Месячный',
                        'yearly' => 'Годовой',
                        'personal' => 'Персональный',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'view' => Pages\ViewPlan::route('/{record}'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
