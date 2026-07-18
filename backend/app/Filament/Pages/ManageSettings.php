<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Email Configuration';

    protected static ?string $title = 'Email Configuration';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $decryptedPassword = '';
        try {
            $encrypted = Setting::get('mail_password', '');
            if (!empty($encrypted)) {
                $decryptedPassword = decrypt($encrypted);
            }
        } catch (\Exception $e) {
            $decryptedPassword = '';
        }

        $this->form->fill([
            'mail_host' => Setting::get('mail_host', 'premium29.web-hosting.com'),
            'mail_port' => Setting::get('mail_port', '465'),
            'mail_username' => Setting::get('mail_username', 'noreply@superdollarsahiwal.com'),
            'mail_password' => $decryptedPassword,
            'mail_encryption' => Setting::get('mail_encryption', 'ssl'),
            'mail_from_address' => Setting::get('mail_from_address', 'noreply@superdollarsahiwal.com'),
            'smtp_from_name' => Setting::get('smtp_from_name', 'AURA Enterprise'),
            'smtp_reply_to' => Setting::get('smtp_reply_to', 'support@superdollarsahiwal.com'),
            'smtp_queue_enabled' => (bool)Setting::get('smtp_queue_enabled', false),
            'test_email_recipient' => Setting::get('test_email_recipient', ''),
            'smtp_connection_status' => Setting::get('smtp_connection_status', 'Unknown'),
            'smtp_last_test_result' => Setting::get('smtp_last_test_result', 'No test performed yet.'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('SMTP Connection')
                            ->schema([
                                TextInput::make('mail_host')
                                    ->label('SMTP Host')
                                    ->required(),
                                TextInput::make('mail_port')
                                    ->label('SMTP Port')
                                    ->required(),
                                TextInput::make('mail_username')
                                    ->label('SMTP Username')
                                    ->required(),
                                TextInput::make('mail_password')
                                    ->label('SMTP Password')
                                    ->password()
                                    ->revealable()
                                    ->required(),
                                Select::make('mail_encryption')
                                    ->label('Encryption Protocol')
                                    ->options([
                                        'none' => 'None',
                                        'ssl' => 'SSL',
                                        'tls' => 'TLS',
                                    ])
                                    ->required(),
                                Toggle::make('smtp_queue_enabled')
                                    ->label('Enable Mail Queue (Asynchronous sending)')
                                    ->default(false),
                            ])->columns(2),

                        Tabs\Tab::make('Sender Profile')
                            ->schema([
                                TextInput::make('mail_from_address')
                                    ->label('From Email Address')
                                    ->email()
                                    ->required(),
                                TextInput::make('smtp_from_name')
                                    ->label('From Name')
                                    ->required(),
                                TextInput::make('smtp_reply_to')
                                    ->label('Reply-To Email Address')
                                    ->email()
                                    ->nullable(),
                            ])->columns(2),

                        Tabs\Tab::make('Diagnostics & Testing')
                            ->schema([
                                TextInput::make('test_email_recipient')
                                    ->label('Recipient Email Address for Testing')
                                    ->placeholder('e.g. test@example.com')
                                    ->email()
                                    ->nullable(),
                                Actions::make([
                                    Action::make('sendTest')
                                        ->label('Send Test Email')
                                        ->action('sendTestEmail')
                                        ->color('primary')
                                        ->icon('heroicon-o-paper-airplane')
                                ]),
                                Placeholder::make('smtp_connection_status_view')
                                    ->label('Connection Status')
                                    ->content(fn () => new HtmlString(
                                        $this->data['smtp_connection_status'] === 'Connected'
                                            ? '<span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-250">Connected</span>'
                                            : ($this->data['smtp_connection_status'] === 'Failed'
                                                ? '<span class="px-2.5 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-250">Failed</span>'
                                                : '<span class="px-2.5 py-1 bg-zinc-50 text-zinc-650 text-xs font-bold rounded-full border border-zinc-250">Unknown</span>')
                                    )),
                                Placeholder::make('smtp_last_test_result_view')
                                    ->label('Last Test Result Details')
                                    ->content(fn () => $this->data['smtp_last_test_result']),
                            ])->columns(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            if ($key === 'mail_password') {
                $value = !empty($value) ? encrypt($value) : '';
            }
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Email configurations saved successfully!')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $state = $this->form->getState();
        $testEmail = $state['test_email_recipient'] ?? null;
        if (empty($testEmail)) {
            Notification::make()->title('Recipient email is required for testing.')->danger()->send();
            return;
        }

        try {
            // Test SMTP socket connection
            $connection = @fsockopen($state['mail_host'], (int)$state['mail_port'], $errno, $errstr, 5);
            if (!$connection) {
                throw new \Exception("Could not open TCP socket connection to {$state['mail_host']}:{$state['mail_port']}. Error: {$errstr} ({$errno})");
            }
            fclose($connection);

            // Dynamically override mail configuration for this send check
            config([
                'mail.mailers.smtp.host' => $state['mail_host'],
                'mail.mailers.smtp.port' => (int)$state['mail_port'],
                'mail.mailers.smtp.username' => $state['mail_username'],
                'mail.mailers.smtp.password' => $state['mail_password'],
                'mail.mailers.smtp.encryption' => $state['mail_encryption'],
                'mail.from.address' => $state['mail_from_address'],
                'mail.from.name' => $state['smtp_from_name'] ?: config('app.name'),
            ]);

            \Illuminate\Support\Facades\Mail::raw(
                "Congratulations! Your SMTP email setup on AURA Enterprise is working perfectly. \n\nConnection details: \nHost: {$state['mail_host']} \nPort: {$state['mail_port']}\nUsername: {$state['mail_username']}", 
                function ($message) use ($testEmail, $state) {
                    $message->to($testEmail)
                        ->subject('AURA Enterprise SMTP Verification Test')
                        ->replyTo($state['smtp_reply_to'] ?? $state['mail_from_address']);
                }
            );

            Setting::set('smtp_connection_status', 'Connected');
            Setting::set('smtp_last_test_result', 'Success: Test email successfully sent at ' . now()->toDateTimeString());

            Notification::make()
                ->title('Test email sent successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Setting::set('smtp_connection_status', 'Failed');
            Setting::set('smtp_last_test_result', 'Error: ' . $e->getMessage());

            Notification::make()
                ->title('SMTP Connection Test Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->mount();
    }
}
