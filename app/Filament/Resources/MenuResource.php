<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    
    protected static ?string $modelLabel = 'Меню';
    protected static ?string $pluralModelLabel = 'Меню';
    protected static ?string $navigationLabel = 'Меню';
    protected static ?string $navigationGroup = 'Контент';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        Forms\Components\DatePicker::make('month')
                            ->label('Месяц')
                            ->required()
                            ->displayFormat('m/Y')
                            ->format('Y-m-01'),
                        
                        Forms\Components\TextInput::make('calories_per_day')
                            ->label('Калорий в день')
                            ->numeric()
                            ->suffix('ккал')
                            ->default(1200),
                    ])->columns(2),
                
                Forms\Components\Section::make('Настройки доступа')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_early_access')
                            ->label('Ранний доступ')
                            ->default(false),
                        
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Дата публикации')
                            ->default(now()),
                        
                        Forms\Components\DateTimePicker::make('early_access_at')
                            ->label('Дата раннего доступа'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Изображение')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Изображение меню')
                            ->image()
                            ->directory('menus')
                            ->maxSize(2048),
                    ]),
                
                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\KeyValue::make('nutrition_summary')
                            ->label('Сводка по питательности')
                            ->keyLabel('Показатель')
                            ->valueLabel('Значение'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Изображение')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('month')
                    ->label('Месяц')
                    ->date('m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('calories_per_day')
                    ->label('Калорий/день')
                    ->suffix(' ккал')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('days_count')
                    ->label('Дней')
                    ->counts('days'),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_early_access')
                    ->label('Ранний доступ')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Дата публикации')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Опубликовано'),
                
                Tables\Filters\TernaryFilter::make('is_early_access')
                    ->label('Ранний доступ'),
                
                Tables\Filters\Filter::make('month')
                    ->form([
                        Forms\Components\DatePicker::make('month_from')
                            ->label('Месяц с'),
                        Forms\Components\DatePicker::make('month_until')
                            ->label('Месяц по'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['month_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('month', '>=', $date),
                            )
                            ->when(
                                $data['month_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('month', '<=', $date),
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
            ->defaultSort('month', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DaysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'view' => Pages\ViewMenu::route('/{record}'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
