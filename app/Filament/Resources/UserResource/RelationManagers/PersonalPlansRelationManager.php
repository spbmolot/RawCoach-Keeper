<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'personalPlans';

    protected static ?string $title = 'Персональные планы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название плана')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->rows(3),
                
                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'draft' => 'Черновик',
                        'active' => 'Активный',
                        'completed' => 'Завершен',
                        'cancelled' => 'Отменен',
                    ])
                    ->required(),
                
                Forms\Components\TextInput::make('target_calories')
                    ->label('Целевая калорийность')
                    ->numeric()
                    ->suffix('ккал'),
                
                Forms\Components\TextInput::make('target_weight')
                    ->label('Целевой вес')
                    ->numeric()
                    ->suffix('кг'),
                
                Forms\Components\DatePicker::make('start_date')
                    ->label('Дата начала')
                    ->required(),
                
                Forms\Components\DatePicker::make('end_date')
                    ->label('Дата окончания'),
                
                Forms\Components\KeyValue::make('preferences')
                    ->label('Предпочтения')
                    ->keyLabel('Параметр')
                    ->valueLabel('Значение'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Черновик',
                        'active' => 'Активный',
                        'completed' => 'Завершен',
                        'cancelled' => 'Отменен',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('target_calories')
                    ->label('Калории')
                    ->suffix(' ккал')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('target_weight')
                    ->label('Целевой вес')
                    ->suffix(' кг')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Начало')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Окончание')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'draft' => 'Черновик',
                        'active' => 'Активный',
                        'completed' => 'Завершен',
                        'cancelled' => 'Отменен',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать персональный план'),
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
