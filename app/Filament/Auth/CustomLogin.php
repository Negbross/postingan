<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseAuthPage;
use Illuminate\Validation\ValidationException;

class CustomLogin extends BaseAuthPage
{
    public function form(Form $form): Form
    {
        return $form->schema([
            $this->getLoginComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
        ])->statePath('data');
    }

    protected function getLoginComponent(): Component
    {
        return TextInput::make('Login')
            ->required()
            ->label('Login')
            ->required()
            ->autofocus()
            ->autocomplete()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data) : array
    {
        $login_type = filter_var($data['Login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $login_type => $data['Login'],
            'password' => $data['password'],
        ];
    }

    protected  function throwFailureValidationException() : never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
