<?php

namespace App\Filament\Resources\EssayAnswers\Pages;

use App\Filament\Resources\EssayAnswers\EssayAnswerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEssayAnswers extends ListRecords
{
    protected static string $resource = EssayAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
