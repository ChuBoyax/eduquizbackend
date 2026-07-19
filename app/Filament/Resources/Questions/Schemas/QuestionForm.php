<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('quiz_id')
                    ->required()
                    ->numeric(),
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
            'multiple_choice' => 'Multiple choice',
            'true_false' => 'True false',
            'fill_in_the_blank' => 'Fill in the blank',
            'essay' => 'Essay',
        ])
                    ->default('multiple_choice')
                    ->required(),
                TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->image(),
                Textarea::make('explanation')
                    ->columnSpanFull(),
            ]);
    }
}
