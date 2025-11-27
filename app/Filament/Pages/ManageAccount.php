<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ManageAccount extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    protected static ?string $navigationLabel = 'My Account';
    
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.manage-account';
    
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Information')
                    ->description('Update your email and password here.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true, table: 'users', column: 'email')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Change Password')
                    ->description('Leave blank to keep your current password.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password')
                            ->currentPassword()
                            ->helperText('Required to change password'),
                        
                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->same('new_password_confirmation')
                            ->helperText('At least 8 characters'),
                        
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $user = Auth::user();
        
        // Update name and email
        $user->name = $data['name'];
        $user->email = $data['email'];
        
        // Update password if provided
        if (!empty($data['new_password'])) {
            $user->password = Hash::make($data['new_password']);
        }
        
        $user->save();
        
        // Clear password fields
        $this->data['current_password'] = null;
        $this->data['new_password'] = null;
        $this->data['new_password_confirmation'] = null;
        
        Notification::make()
            ->title('Account updated successfully')
            ->success()
            ->send();
    }
    
    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
