<?php

namespace App\Filament\Resources\EssayAnswers;

use App\Filament\Resources\EssayAnswers\Pages\CreateEssayAnswer;
use App\Filament\Resources\EssayAnswers\Pages\EditEssayAnswer;
use App\Filament\Resources\EssayAnswers\Pages\ListEssayAnswers;
use App\Filament\Resources\EssayAnswers\Schemas\EssayAnswerForm;
use App\Filament\Resources\EssayAnswers\Tables\EssayAnswersTable;
use App\Models\EssayAnswer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EssayAnswerResource extends Resource
{
    protected static ?string $model = EssayAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'essayanswer';

    public static function form(Schema $schema): Schema
    {
        return EssayAnswerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EssayAnswersTable::configure($table);
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
            'index' => ListEssayAnswers::route('/'),
            'create' => CreateEssayAnswer::route('/create'),
            'edit' => EditEssayAnswer::route('/{record}/edit'),
        ];
    }
}
