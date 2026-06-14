<?php

namespace App\Filament\Concerns;

trait RedirecionaParaListagem
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}