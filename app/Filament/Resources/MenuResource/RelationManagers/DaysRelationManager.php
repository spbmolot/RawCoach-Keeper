<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DaysRelationManager extends RelationManager
{
    protected static string $relationship = 'days';

    protected static ?string $title = 'Дни меню';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('day_number')
                    ->label('День')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(31),
                
                Forms\Components\DatePicker::make('date')
                    ->label('Дата')
                    ->required(),
                
                Forms\Components\TextInput::make('total_calories')
                    ->label('Общая калорийность')
                    ->numeric()
                    ->suffix('ккал'),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Заметки')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day_number')
            ->columns([
                Tables\Columns\TextColumn::make('day_number')
                    ->label('День')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('date')
                    ->label('Дата')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_calories')
                    ->label('Калории')
                    ->suffix(' ккал')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('meals_count')
                    ->label('Приемов пищи')
                    ->counts('meals'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить день'),
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
            ->defaultSort('day_number');
    }
}
