<?php

namespace App\Filament\Resources\EssayAnswers\Pages;

use App\Filament\Resources\EssayAnswers\EssayAnswerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEssayAnswer extends EditRecord
{
    protected static string $resource = EssayAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
