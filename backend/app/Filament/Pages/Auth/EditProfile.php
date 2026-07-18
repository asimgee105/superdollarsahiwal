<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                FileUpload::make('avatar')
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('avatars')
                    ->label('Profile Picture')
                    ->afterStateHydrated(function ($component, $state, $record) {
                        $component->state($record->profile?->avatar);
                    })
                    ->saveRelationshipsUsing(function ($component, $state, $record) {
                        $record->profile()->updateOrCreate(
                            [],
                            ['avatar' => $state]
                        );
                    }),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
