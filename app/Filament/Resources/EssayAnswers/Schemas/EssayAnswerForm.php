<?php

namespace App\Filament\Resources\EssayAnswers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EssayAnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('room_participant_id')
                    ->required()
                    ->numeric(),
                TextInput::make('question_id')
                    ->required()
                    ->numeric(),
                Textarea::make('answer')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('points_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('teacher_feedback')
                    ->columnSpanFull(),
                Toggle::make('is_graded')
                    ->required(),
            ]);
    }
}
