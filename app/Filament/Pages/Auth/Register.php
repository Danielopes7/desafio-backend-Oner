<?php
namespace App\Filament\Pages\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Register as BaseRegister;
class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getTypeFormComponent(),
                        $this->getCpfFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
 
    protected function getTypeFormComponent(): Component
    {
        return Select::make('type')
            ->options([
                'customer' => 'Customer',
                'shopkeeper' => 'Shopkeeper',
            ])
            ->default('customer')
            ->required();
    }

    protected function getCpfFormComponent(): Component
    {
        return TextInput::make('cpf_cnpj')
            ->label('Cpf/CNPJ')
            ->required();
    }
}