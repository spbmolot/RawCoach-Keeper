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
                        
                        Forms\Components\TextInput::make('month')
                            ->label('Месяц')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->required(),
                        
                        Forms\Components\TextInput::make('year')
                            ->label('Год')
                            ->numeric()
                            ->minValue(2020)
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Настройки доступа')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Опубликовано')
                            ->default(false),
                        
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Дата публикации')
                            ->default(now()),
                        
                        Forms\Components\DatePicker::make('visible_from')
                            ->label('Видимо с даты')
                            ->helperText('Меню станет доступно пользователям начиная с этой даты. Оставьте пустым для немедленной видимости.'),
                        
                        Forms\Components\Toggle::make('is_personal')
                            ->label('Персональное меню')
                            ->helperText('Персональное меню видно только выбранному пользователю')
                            ->reactive(),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('is_personal'))
                            ->required(fn (callable $get) => $get('is_personal'))
                            ->helperText('Выберите пользователя для персонального меню'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Изображение и файлы')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Обложка меню')
                            ->image()
                            ->directory('menus')
                            ->maxSize(2048),
                        
                        Forms\Components\FileUpload::make('pdf_file')
                            ->label('PDF файл')
                            ->directory('menus/pdf')
                            ->acceptedFileTypes(['application/pdf']),
                        
                        Forms\Components\FileUpload::make('excel_file')
                            ->label('Excel файл')
                            ->directory('menus/excel'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Дополнительно')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Заметки')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Обложка')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('month')
                    ->label('Месяц')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('year')
                    ->label('Год')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('days_count')
                    ->label('Дней')
                    ->counts('days'),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_personal')
                    ->label('Персональное')
                    ->boolean()
                    ->trueIcon('heroicon-o-user')
                    ->falseIcon('heroicon-o-users'),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Пользователь')
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('visible_from')
                    ->label('Видимо с')
                    ->date()
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
                
                Tables\Filters\TernaryFilter::make('is_personal')
                    ->label('Персональное'),
                
                Tables\Filters\SelectFilter::make('year')
                    ->label('Год')
                    ->options(fn () => Menu::query()->distinct()->pluck('year', 'year')->toArray()),
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
