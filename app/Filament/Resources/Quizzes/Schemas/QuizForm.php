<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'])
                    ->default('draft')
                    ->required(),
                TextInput::make('time_limit')
                    ->numeric()->nullable()
                    ->label('Time Limit (minutes)'),
                Toggle::make('shuffle_questions')
                    ->required(),
                Toggle::make('shuffle_options')
                    ->required(),
                Toggle::make('show_results_immediately')
                    ->required()
                    ->default(true),
            ]);
    }
}
