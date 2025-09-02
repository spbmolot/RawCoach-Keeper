<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $navigationGroup = 'Пользователи';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email подтвержден'),
                        
                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Профиль')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Дата рождения'),
                        
                        Forms\Components\Select::make('gender')
                            ->label('Пол')
                            ->options([
                                'male' => 'Мужской',
                                'female' => 'Женский',
                                'other' => 'Другой',
                            ]),
                        
                        Forms\Components\TextInput::make('height')
                            ->label('Рост (см)')
                            ->numeric()
                            ->suffix('см'),
                        
                        Forms\Components\TextInput::make('weight')
                            ->label('Вес (кг)')
                            ->numeric()
                            ->suffix('кг'),
                        
                        Forms\Components\Select::make('activity_level')
                            ->label('Уровень активности')
                            ->options([
                                'sedentary' => 'Малоподвижный',
                                'light' => 'Легкая активность',
                                'moderate' => 'Умеренная активность',
                                'active' => 'Активный',
                                'very_active' => 'Очень активный',
                            ]),
                    ])->columns(2),
                
                Forms\Components\Section::make('Роли и права')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Роли')
                            ->relationship('roles', 'name')
                            ->columns(2),
                    ]),
                
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->label('О себе')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        Forms\Components\KeyValue::make('preferences')
                            ->label('Предпочтения')
                            ->keyLabel('Параметр')
                            ->valueLabel('Значение'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email подтвержден')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at)),
                
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Роли')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Администратор',
                        'editor' => 'Редактор',
                        'nutritionist' => 'Нутрициолог',
                        'advertiser' => 'Рекламодатель',
                        'subscriber_basic' => 'Базовый подписчик',
                        'subscriber_yearly' => 'Годовой подписчик',
                        'subscriber_personal' => 'Персональный подписчик',
                        'user' => 'Пользователь',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Подписок')
                    ->counts('subscriptions'),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Дата рождения')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Зарегистрирован')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Последний вход')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email подтвержден')
                    ->nullable(),
                
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Роль')
                    ->relationship('roles', 'name')
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность'),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Зарегистрирован с'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Зарегистрирован по'),
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
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\SubscriptionsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\PersonalPlansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
