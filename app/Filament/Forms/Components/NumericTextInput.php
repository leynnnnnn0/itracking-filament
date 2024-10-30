<?php

namespace App\Forms\Components;

use Filament\Forms\Components\TextInput;

class NumericTextInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric()
            ->extraInputAttributes([
                'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
            ]);
    }
}

