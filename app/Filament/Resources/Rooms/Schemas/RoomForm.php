<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('quiz_id')
                    ->required()
                    ->numeric(),
                TextInput::make('teacher_id')
                    ->required()
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                Select::make('status')
                    ->options(['waiting' => 'Waiting', 'ongoing' => 'Ongoing', 'finished' => 'Finished'])
                    ->default('waiting')
                    ->required(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('ended_at'),
            ]);
    }
}
