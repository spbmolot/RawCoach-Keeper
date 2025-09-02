<?php

namespace App\Filament\Resources\RecipeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'ingredients';

    protected static ?string $title = 'Ингредиенты';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название ингредиента')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('amount')
                    ->label('Количество')
                    ->numeric()
                    ->required(),
                
                Forms\Components\TextInput::make('unit')
                    ->label('Единица измерения')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('г, мл, шт, ст.л.'),
                
                Forms\Components\TextInput::make('calories_per_100g')
                    ->label('Калорий на 100г')
                    ->numeric()
                    ->suffix('ккал'),
                
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ингредиент')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Количество')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('unit')
                    ->label('Единица')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('calories_per_100g')
                    ->label('Калории/100г')
                    ->suffix(' ккал')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить ингредиент'),
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
            ->defaultSort('sort_order');
    }
}
