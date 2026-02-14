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
                        
                        Forms\Components\Select::make('category')
                            ->label('Категория')
                            ->options([
                                'breakfast' => 'Завтрак',
                                'lunch' => 'Обед',
                                'dinner' => 'Ужин',
                                'snack' => 'Перекус',
                                'dessert' => 'Десерт',
                            ]),
                        
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
                        Forms\Components\FileUpload::make('main_image')
                            ->label('Главное изображение')
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
                
                Forms\Components\Section::make('Питательная ценность')
                    ->schema([
                        Forms\Components\TextInput::make('calories')
                            ->label('Калории')
                            ->numeric()
                            ->suffix('ккал'),
                        
                        Forms\Components\TextInput::make('proteins')
                            ->label('Белки')
                            ->numeric()
                            ->suffix('г'),
                        
                        Forms\Components\TextInput::make('fats')
                            ->label('Жиры')
                            ->numeric()
                            ->suffix('г'),
                        
                        Forms\Components\TextInput::make('carbs')
                            ->label('Углеводы')
                            ->numeric()
                            ->suffix('г'),
                    ])->columns(4),
                
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Заметки')
                            ->rows(3),
                        
                        Forms\Components\TagsInput::make('dietary_tags')
                            ->label('Диетические теги')
                            ->placeholder('Добавить тег'),
                        
                        Forms\Components\TagsInput::make('allergens')
                            ->label('Аллергены')
                            ->placeholder('Добавить аллерген'),
                        
                        Forms\Components\Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_free')
                            ->label('Бесплатный')
                            ->helperText('Доступен без подписки (Freemium)')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Изображение')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Категория')
                    ->colors([
                        'success' => 'breakfast',
                        'primary' => 'lunch',
                        'warning' => 'dinner',
                        'secondary' => 'snack',
                        'info' => 'dessert',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'breakfast' => 'Завтрак',
                        'lunch' => 'Обед',
                        'dinner' => 'Ужин',
                        'snack' => 'Перекус',
                        'dessert' => 'Десерт',
                        default => $state ?? '-',
                    }),
                
                Tables\Columns\BadgeColumn::make('difficulty')
                    ->label('Сложность')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'medium',
                        'danger' => 'hard',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'easy' => 'Легко',
                        'medium' => 'Средне',
                        'hard' => 'Сложно',
                        default => $state ?? '-',
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
                
                Tables\Columns\TextColumn::make('calories')
                    ->label('Ккал')
                    ->suffix(' ккал')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_free')
                    ->label('Бесплатный')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-open')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Категория')
                    ->options([
                        'breakfast' => 'Завтрак',
                        'lunch' => 'Обед',
                        'dinner' => 'Ужин',
                        'snack' => 'Перекус',
                        'dessert' => 'Десерт',
                    ]),
                
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('Сложность')
                    ->options([
                        'easy' => 'Легко',
                        'medium' => 'Средне',
                        'hard' => 'Сложно',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Опубликовано'),
                
                Tables\Filters\TernaryFilter::make('is_free')
                    ->label('Бесплатный'),
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
