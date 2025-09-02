<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;
    
    protected static ?string $modelLabel = 'Рецепт';
    protected static ?string $pluralModelLabel = 'Рецепты';
    protected static ?string $navigationLabel = 'Рецепты';
    protected static ?string $navigationGroup = 'Контент';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

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
                        
                        Forms\Components\Select::make('meal_type')
                            ->label('Тип приема пищи')
                            ->options([
                                'breakfast' => 'Завтрак',
                                'lunch' => 'Обед',
                                'dinner' => 'Ужин',
                                'snack' => 'Перекус',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('difficulty')
                            ->label('Сложность')
                            ->options([
                                'easy' => 'Легко',
                                'medium' => 'Средне',
                                'hard' => 'Сложно',
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Время приготовления')
                    ->schema([
                        Forms\Components\TextInput::make('prep_time')
                            ->label('Время подготовки (мин)')
                            ->numeric()
                            ->suffix('мин'),
                        
                        Forms\Components\TextInput::make('cook_time')
                            ->label('Время готовки (мин)')
                            ->numeric()
                            ->suffix('мин'),
                        
                        Forms\Components\TextInput::make('servings')
                            ->label('Количество порций')
                            ->numeric()
                            ->default(1),
                    ])->columns(3),
                
                Forms\Components\Section::make('Изображение')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Изображение рецепта')
                            ->image()
                            ->directory('recipes')
                            ->maxSize(2048),
                    ]),
                
                Forms\Components\Section::make('Инструкции')
                    ->schema([
                        Forms\Components\RichEditor::make('instructions')
                            ->label('Пошаговые инструкции')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\Textarea::make('tips')
                            ->label('Советы по приготовлению')
                            ->rows(3),
                        
                        Forms\Components\TagsInput::make('tags')
                            ->label('Теги')
                            ->placeholder('Добавить тег'),
                        
                        Forms\Components\Toggle::make('is_vegetarian')
                            ->label('Вегетарианское'),
                        
                        Forms\Components\Toggle::make('is_vegan')
                            ->label('Веганское'),
                        
                        Forms\Components\Toggle::make('is_gluten_free')
                            ->label('Без глютена'),
                        
                        Forms\Components\Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->default(false),
                    ])->columns(2),
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
                
                Tables\Columns\BadgeColumn::make('meal_type')
                    ->label('Тип')
                    ->colors([
                        'success' => 'breakfast',
                        'primary' => 'lunch',
                        'warning' => 'dinner',
                        'secondary' => 'snack',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'breakfast' => 'Завтрак',
                        'lunch' => 'Обед',
                        'dinner' => 'Ужин',
                        'snack' => 'Перекус',
                        default => $state,
                    }),
                
                Tables\Columns\BadgeColumn::make('difficulty')
                    ->label('Сложность')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'medium',
                        'danger' => 'hard',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'easy' => 'Легко',
                        'medium' => 'Средне',
                        'hard' => 'Сложно',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('prep_time')
                    ->label('Подготовка')
                    ->suffix(' мин')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('cook_time')
                    ->label('Готовка')
                    ->suffix(' мин')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('servings')
                    ->label('Порций')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_vegetarian')
                    ->label('🥬')
                    ->boolean()
                    ->tooltip('Вегетарианское'),
                
                Tables\Columns\IconColumn::make('is_vegan')
                    ->label('🌱')
                    ->boolean()
                    ->tooltip('Веганское'),
                
                Tables\Columns\IconColumn::make('is_gluten_free')
                    ->label('🌾')
                    ->boolean()
                    ->tooltip('Без глютена'),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('meal_type')
                    ->label('Тип приема пищи')
                    ->options([
                        'breakfast' => 'Завтрак',
                        'lunch' => 'Обед',
                        'dinner' => 'Ужин',
                        'snack' => 'Перекус',
                    ]),
                
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('Сложность')
                    ->options([
                        'easy' => 'Легко',
                        'medium' => 'Средне',
                        'hard' => 'Сложно',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_vegetarian')
                    ->label('Вегетарианское'),
                
                Tables\Filters\TernaryFilter::make('is_vegan')
                    ->label('Веганское'),
                
                Tables\Filters\TernaryFilter::make('is_gluten_free')
                    ->label('Без глютена'),
                
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Опубликовано'),
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
            RelationManagers\IngredientsRelationManager::class,
            RelationManagers\NutritionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'view' => Pages\ViewRecipe::route('/{record}'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
