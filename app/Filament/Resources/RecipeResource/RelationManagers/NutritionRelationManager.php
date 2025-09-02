<?php

namespace App\Filament\Resources\RecipeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NutritionRelationManager extends RelationManager
{
    protected static string $relationship = 'nutrition';

    protected static ?string $title = 'Пищевая ценность';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('calories')
                    ->label('Калории')
                    ->numeric()
                    ->suffix('ккал')
                    ->required(),
                
                Forms\Components\TextInput::make('protein')
                    ->label('Белки')
                    ->numeric()
                    ->suffix('г')
                    ->step(0.1),
                
                Forms\Components\TextInput::make('carbs')
                    ->label('Углеводы')
                    ->numeric()
                    ->suffix('г')
                    ->step(0.1),
                
                Forms\Components\TextInput::make('fat')
                    ->label('Жиры')
                    ->numeric()
                    ->suffix('г')
                    ->step(0.1),
                
                Forms\Components\TextInput::make('fiber')
                    ->label('Клетчатка')
                    ->numeric()
                    ->suffix('г')
                    ->step(0.1),
                
                Forms\Components\TextInput::make('sugar')
                    ->label('Сахар')
                    ->numeric()
                    ->suffix('г')
                    ->step(0.1),
                
                Forms\Components\TextInput::make('sodium')
                    ->label('Натрий')
                    ->numeric()
                    ->suffix('мг')
                    ->step(0.1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('calories')
            ->columns([
                Tables\Columns\TextColumn::make('calories')
                    ->label('Калории')
                    ->suffix(' ккал')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('protein')
                    ->label('Белки')
                    ->suffix(' г')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('carbs')
                    ->label('Углеводы')
                    ->suffix(' г')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fat')
                    ->label('Жиры')
                    ->suffix(' г')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fiber')
                    ->label('Клетчатка')
                    ->suffix(' г')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить данные о питательности'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
