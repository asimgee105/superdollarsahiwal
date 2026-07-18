<?php

namespace App\Filament\Pages;

use App\Models\HomepageLayout;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class ManageWebsiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Global Website Settings';

    protected static ?string $navigationGroup = 'Site Builder';

    protected static string $view = 'filament.pages.manage-website-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Load settings from database
        $settings = Setting::pluck('value', 'key')->toArray();

        // Standardize json decodes
        foreach ($settings as $key => $value) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $settings[$key] = $decoded;
            }
        }

        // Decrypt mail password if exists
        try {
            $encrypted = $settings['mail_password'] ?? '';
            if (!empty($encrypted)) {
                $settings['mail_password'] = decrypt($encrypted);
            }
        } catch (\Exception $e) {
            $settings['mail_password'] = '';
        }

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General Meta')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->nullable(),
                                Forms\Components\TextInput::make('site_tagline'),
                                Forms\Components\TextInput::make('logo_url')
                                    ->label('Logo Image URL')
                                    ->placeholder('e.g. /images/logo.png'),
                                Forms\Components\FileUpload::make('logo_file')
                                    ->label('Or Upload Logo')
                                    ->directory('site-assets')
                                    ->image()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('logo_url', '/storage/'.$state) : null),
                                Forms\Components\TextInput::make('favicon_url')
                                    ->label('Favicon URL'),
                                Forms\Components\FileUpload::make('favicon_file')
                                    ->label('Or Upload Favicon')
                                    ->directory('site-assets')
                                    ->image()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('favicon_url', '/storage/'.$state) : null),
                                Forms\Components\TextInput::make('logo_light_url')
                                    ->label('Light Theme Logo URL'),
                                Forms\Components\FileUpload::make('logo_light_file')
                                    ->label('Or Upload Light Logo')
                                    ->directory('site-assets')
                                    ->image()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('logo_light_url', '/storage/'.$state) : null),
                                Forms\Components\TextInput::make('logo_dark_url')
                                    ->label('Dark Theme Logo URL'),
                                Forms\Components\FileUpload::make('logo_dark_file')
                                    ->label('Or Upload Dark Logo')
                                    ->directory('site-assets')
                                    ->image()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('logo_dark_url', '/storage/'.$state) : null),
                                Forms\Components\Toggle::make('dark_mode_ready')
                                    ->default(true),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Theme & Styles')
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->default('#ff3f6c')
                                    ->nullable(),
                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->default('#1a1a1a')
                                    ->nullable(),
                                Forms\Components\ColorPicker::make('success_color')
                                    ->default('#10b981'),
                                Forms\Components\ColorPicker::make('warning_color')
                                    ->default('#f59e0b'),
                                Forms\Components\ColorPicker::make('error_color')
                                    ->default('#ef4444'),
                                Forms\Components\Select::make('typography_font')
                                    ->options([
                                        'Outfit' => 'Outfit (Myntra Default)',
                                        'Inter' => 'Inter',
                                        'Roboto' => 'Roboto',
                                        'Outfit, sans-serif' => 'System Sans',
                                    ])
                                    ->default('Outfit')
                                    ->nullable(),
                                Forms\Components\Select::make('button_style')
                                    ->options([
                                        'pill' => 'Fully Rounded (Pill)',
                                        'rounded' => 'Slightly Rounded',
                                        'sharp' => 'Sharp Edges',
                                    ])
                                    ->default('rounded'),
                                Forms\Components\TextInput::make('border_radius')
                                    ->default('4px')
                                    ->placeholder('e.g. 4px, 8px, 12px'),
                                Forms\Components\TextInput::make('container_width')
                                    ->default('1280px')
                                    ->placeholder('e.g. 1280px, 1440px'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Layouts Configuration')
                            ->schema([
                                Forms\Components\Select::make('active_homepage_layout')
                                    ->options(function () {
                                        return HomepageLayout::pluck('name', 'id')->toArray();
                                    })
                                    ->label('Active Layout Manager Profile')
                                    ->nullable(),
                                Forms\Components\TextInput::make('header_announcement_bar')
                                    ->label('Header Announcement Text')
                                    ->default('FLAT $30.00 OFF + FREE SHIPPING ON YOUR FIRST ORDER'),
                                Forms\Components\Toggle::make('sticky_header')
                                    ->default(true),
                                Forms\Components\Toggle::make('transparent_header')
                                    ->default(false),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Localisation')
                            ->schema([
                                Forms\Components\TextInput::make('currency')
                                    ->default('Rs')
                                    ->nullable(),
                                Forms\Components\TextInput::make('timezone')
                                    ->default('Asia/Karachi')
                                    ->nullable(),
                                Forms\Components\TextInput::make('language')
                                    ->default('en')
                                    ->nullable(),
                            ])->columns(3),

                        Forms\Components\Tabs\Tab::make('Contact & Social')
                            ->schema([
                                Forms\Components\TextInput::make('contact_phone')
                                    ->default('080-40011450'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->default('support@myntra.com'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->default('Buildings Alyssa, Embassy Tech Village, Bengaluru, India'),
                                Forms\Components\TextInput::make('social_facebook')
                                    ->placeholder('URL'),
                                Forms\Components\TextInput::make('social_twitter')
                                    ->placeholder('URL'),
                                Forms\Components\TextInput::make('social_youtube')
                                    ->placeholder('URL'),
                                Forms\Components\TextInput::make('social_instagram')
                                    ->placeholder('URL'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Authentication & Login Options')
                            ->schema([
                                Forms\Components\Section::make('Login Methods Configuration')
                                    ->schema([
                                        Forms\Components\Toggle::make('auth_email_password_enabled')
                                            ->label('Enable Email + Password Login')
                                            ->default(true),
                                        Forms\Components\Toggle::make('auth_email_otp_enabled')
                                            ->label('Enable Email + OTP Login')
                                            ->default(true),
                                        Forms\Components\Toggle::make('auth_mobile_password_enabled')
                                            ->label('Enable Mobile + Password Login')
                                            ->default(true),
                                        Forms\Components\Toggle::make('auth_mobile_otp_enabled')
                                            ->label('Enable Mobile + OTP Login')
                                            ->default(true),
                                    ])->columns(2),

                                Forms\Components\Section::make('OAuth Authentication Providers')
                                    ->schema([
                                        Forms\Components\Toggle::make('oauth_google_enabled')->label('Google Login'),
                                        Forms\Components\TextInput::make('oauth_google_client_id')->label('Google Client ID'),
                                        Forms\Components\TextInput::make('oauth_google_client_secret')->label('Google Client Secret')->password(),
                                        Forms\Components\Toggle::make('oauth_facebook_enabled')->label('Facebook Login'),
                                        Forms\Components\TextInput::make('oauth_facebook_client_id')->label('Facebook Client ID'),
                                        Forms\Components\TextInput::make('oauth_facebook_client_secret')->label('Facebook Client Secret')->password(),
                                        Forms\Components\Toggle::make('oauth_apple_enabled')->label('Apple Login'),
                                        Forms\Components\TextInput::make('oauth_apple_client_id')->label('Apple Client ID'),
                                        Forms\Components\TextInput::make('oauth_apple_client_secret')->label('Apple Client Secret')->password(),
                                        Forms\Components\Toggle::make('oauth_github_enabled')->label('GitHub Login'),
                                        Forms\Components\TextInput::make('oauth_github_client_id')->label('GitHub Client ID'),
                                        Forms\Components\TextInput::make('oauth_github_client_secret')->label('GitHub Client Secret')->password(),
                                        Forms\Components\Toggle::make('oauth_microsoft_enabled')->label('Microsoft Login'),
                                        Forms\Components\TextInput::make('oauth_microsoft_client_id')->label('Microsoft Client ID'),
                                        Forms\Components\TextInput::make('oauth_microsoft_client_secret')->label('Microsoft Client Secret')->password(),
                                        Forms\Components\Toggle::make('oauth_linkedin_enabled')->label('LinkedIn Login'),
                                        Forms\Components\TextInput::make('oauth_linkedin_client_id')->label('LinkedIn Client ID'),
                                        Forms\Components\TextInput::make('oauth_linkedin_client_secret')->label('LinkedIn Client Secret')->password(),
                                        Forms\Components\Toggle::make('oauth_twitter_enabled')->label('X (Twitter) Login'),
                                        Forms\Components\TextInput::make('oauth_twitter_client_id')->label('X Client ID'),
                                        Forms\Components\TextInput::make('oauth_twitter_client_secret')->label('X Client Secret')->password(),
                                    ])->columns(3),

                                Forms\Components\Section::make('OTP Parameters Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('otp_length')
                                            ->label('OTP Code Length (digits)')
                                            ->numeric()
                                            ->default(6),
                                        Forms\Components\TextInput::make('otp_expiry_minutes')
                                            ->label('OTP Code Expiration (minutes)')
                                            ->numeric()
                                            ->default(10),
                                        Forms\Components\TextInput::make('otp_resend_delay_seconds')
                                            ->label('Resend Delay Interval (seconds)')
                                            ->numeric()
                                            ->default(60),
                                        Forms\Components\TextInput::make('otp_max_retry_attempts')
                                            ->label('Max Invalid Retry Attempts')
                                            ->numeric()
                                            ->default(5),
                                    ])->columns(2),

                                Forms\Components\Section::make('Phone OTP Sign-In Providers')
                                    ->schema([
                                        Forms\Components\Toggle::make('phone_login_enabled')->label('Enable Mobile Login'),
                                        Forms\Components\Select::make('phone_otp_provider')
                                            ->options([
                                                'twilio' => 'Twilio SMS Gateway',
                                                'vonage' => 'Vonage Integration',
                                                'msg91' => 'MSG91 Provider',
                                                'local' => 'Local SMS Gateway',
                                            ])->default('twilio'),
                                        Forms\Components\TextInput::make('sms_api_key')->label('SMS Provider API Key'),
                                    ])->columns(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('Payment Gateways')
                            ->schema([
                                Forms\Components\Section::make('Stripe Checkout')
                                    ->schema([
                                        Forms\Components\Toggle::make('payment_stripe_enabled')->label('Enable Stripe'),
                                        Forms\Components\Toggle::make('payment_stripe_sandbox')->label('Sandbox Mode'),
                                        Forms\Components\TextInput::make('payment_stripe_key')->label('Publishable Key'),
                                        Forms\Components\TextInput::make('payment_stripe_secret')->label('Secret Key')->password(),
                                        Forms\Components\TextInput::make('payment_stripe_webhook')->label('Webhook Secret'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Express Checkout Gateways')
                                    ->schema([
                                        Forms\Components\Toggle::make('payment_googlepay_enabled')->label('Enable Google Pay'),
                                        Forms\Components\Toggle::make('payment_paypal_enabled')->label('Enable PayPal'),
                                        Forms\Components\Toggle::make('payment_applepay_enabled')->label('Enable Apple Pay'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Local Payments (JazzCash, EasyPaisa)')
                                    ->schema([
                                        Forms\Components\Toggle::make('payment_jazzcash_enabled')->label('Enable JazzCash'),
                                        Forms\Components\TextInput::make('payment_jazzcash_merchant_id')->label('Merchant ID'),
                                        Forms\Components\TextInput::make('payment_jazzcash_password')->label('Secure Password')->password(),
                                        Forms\Components\Toggle::make('payment_easypaisa_enabled')->label('Enable EasyPaisa'),
                                        Forms\Components\TextInput::make('payment_easypaisa_hash_key')->label('EasyPaisa Hash Key')->password(),
                                    ])->columns(3),

                                Forms\Components\Section::make('Manual Options & Bank Transfers')
                                    ->schema([
                                        Forms\Components\Toggle::make('payment_cod_enabled')->label('Cash On Delivery (COD)')->default(true),
                                        Forms\Components\Toggle::make('payment_bank_transfer_enabled')->label('Direct Bank Transfer'),
                                        Forms\Components\Textarea::make('payment_bank_details')
                                            ->label('Bank Account Details')
                                            ->placeholder('e.g. Meezan Bank, Account Number: XXXXXX'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Shipping Rules')
                            ->schema([
                                Forms\Components\Section::make('Shipping Options Matrix')
                                    ->schema([
                                        Forms\Components\Toggle::make('shipping_free_enabled')->label('Enable Free Shipping Threshold'),
                                        Forms\Components\TextInput::make('shipping_free_threshold')
                                            ->label('Free Shipping Order Minimum (Rs.)')
                                            ->numeric()
                                            ->default(3000),
                                        Forms\Components\TextInput::make('shipping_flat_cost')
                                            ->label('Flat Rate Shipping Charges (Rs.)')
                                            ->numeric()
                                            ->default(150),
                                        Forms\Components\TextInput::make('shipping_weight_base')
                                            ->label('Weight-Based Charges (per KG)')
                                            ->numeric()
                                            ->default(50),
                                        Forms\Components\Select::make('shipping_courier_provider')
                                            ->options([
                                                'tcs' => 'TCS Express Pakistan',
                                                'leopards' => 'Leopards Courier Service',
                                                'trax' => 'TRAX Swift Logistics',
                                                'dhl' => 'DHL International',
                                            ])->default('trax'),
                                    ])->columns(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('AI Configuration')
                            ->schema([
                                Forms\Components\Section::make('Centralized LLM Integration')
                                    ->schema([
                                        Forms\Components\Select::make('ai_default_provider')
                                            ->options([
                                                'openai' => 'OpenAI GPT Models',
                                                'gemini' => 'Google Gemini AI',
                                                'claude' => 'Anthropic Claude Engine',
                                                'openrouter' => 'OpenRouter Multimodal API',
                                                'ollama' => 'Self-Hosted Ollama / Local LLM',
                                            ])->default('openai')
                                            ->live(),
                                        Forms\Components\TextInput::make('ai_openai_key')->label('OpenAI API Key')->password(),
                                        Forms\Components\TextInput::make('ai_gemini_key')->label('Gemini API Key')->password(),
                                        Forms\Components\TextInput::make('ai_claude_key')->label('Claude API Key')->password(),
                                        Forms\Components\TextInput::make('ai_default_model')
                                            ->label('Default LLM Model Name')
                                            ->default('gpt-4o-mini')
                                            ->placeholder('e.g. gpt-4o-mini, gemini-1.5-pro'),
                                        Forms\Components\TextInput::make('ai_temperature')
                                            ->label('Temperature (Creativity)')
                                            ->numeric()
                                            ->default(0.7),
                                    ])->columns(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('Email & Notifications')
                            ->schema([
                                Forms\Components\Section::make('SMTP / SendGrid / Amazon SES parameters')
                                    ->schema([
                                        Forms\Components\Select::make('mail_driver')
                                            ->options([
                                                'smtp' => 'SMTP Server Mailer',
                                                'mailgun' => 'Mailgun SMTP API',
                                                'sendgrid' => 'SendGrid Email Service',
                                                'ses' => 'Amazon Simple Email Service (SES)',
                                            ])->default('smtp'),
                                        Forms\Components\TextInput::make('mail_host')->label('Mail Host')->placeholder('smtp.mailtrap.io'),
                                        Forms\Components\TextInput::make('mail_port')->label('Mail Port')->placeholder('2525'),
                                        Forms\Components\TextInput::make('mail_username')->label('SMTP Username'),
                                        Forms\Components\TextInput::make('mail_password')
                                            ->label('SMTP Password')
                                            ->password()
                                            ->revealable(),
                                        Forms\Components\Select::make('mail_encryption')
                                            ->label('Encryption Protocol')
                                            ->options([
                                                'none' => 'None',
                                                'ssl' => 'SSL',
                                                'tls' => 'TLS',
                                            ])->default('ssl'),
                                        Forms\Components\TextInput::make('mail_from_address')->label('Sender Address'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Notification Channels Toggle')
                                    ->schema([
                                        Forms\Components\Toggle::make('notify_email_enabled')->label('Email Notifications')->default(true),
                                        Forms\Components\Toggle::make('notify_sms_enabled')->label('SMS Text Alerts'),
                                        Forms\Components\Toggle::make('notify_whatsapp_enabled')->label('WhatsApp Order Updates'),
                                        Forms\Components\Toggle::make('notify_push_enabled')->label('PWA Push Alerts'),
                                        Forms\Components\Toggle::make('notify_slack_enabled')->label('Slack Team Notifications'),
                                        Forms\Components\Toggle::make('notify_telegram_enabled')->label('Telegram Bot Alerts'),
                                    ])->columns(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('Storage & Search')
                            ->schema([
                                Forms\Components\Section::make('Asset Storage Disk Config')
                                    ->schema([
                                        Forms\Components\Select::make('storage_driver')
                                            ->options([
                                                'public' => 'Local Public Directory',
                                                's3' => 'Amazon S3 Bucket',
                                                'r2' => 'Cloudflare R2 Storage',
                                                'gcs' => 'Google Cloud Storage',
                                            ])->default('public'),
                                        Forms\Components\TextInput::make('storage_bucket_name')->label('Bucket / Container Name'),
                                        Forms\Components\TextInput::make('storage_aws_key')->label('Access Key ID'),
                                        Forms\Components\TextInput::make('storage_aws_secret')->label('Secret Access Key')->password(),
                                        Forms\Components\TextInput::make('storage_endpoint_url')->label('Custom Endpoint URL'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Enterprise Search Engine')
                                    ->schema([
                                        Forms\Components\Select::make('search_driver')
                                            ->options([
                                                'database' => 'Standard Database Search',
                                                'meilisearch' => 'Meilisearch (Fast Self-Hosted)',
                                                'algolia' => 'Algolia Search API',
                                                'elasticsearch' => 'ElasticSearch Node cluster',
                                            ])->default('database'),
                                        Forms\Components\TextInput::make('search_endpoint_url')->label('Search Server URL'),
                                        Forms\Components\TextInput::make('search_api_key')->label('Search Admin API Key')->password(),
                                    ])->columns(3),
                            ]),

                        Forms\Components\Tabs\Tab::make('Cache & Security')
                            ->schema([
                                Forms\Components\Section::make('Cache Platforms')
                                    ->schema([
                                        Forms\Components\Select::make('cache_driver')
                                            ->options([
                                                'file' => 'Local File Cache',
                                                'database' => 'MySQL Database Cache Table',
                                                'redis' => 'Redis Cluster (Recommended)',
                                                'memcached' => 'Memcached Instance',
                                            ])->default('file'),
                                    ])->columns(1),

                                Forms\Components\Section::make('Brute-force reCAPTCHA / Cloudflare Turnstile')
                                    ->schema([
                                        Forms\Components\Toggle::make('security_recaptcha_enabled')->label('Google reCAPTCHA v3'),
                                        Forms\Components\TextInput::make('security_recaptcha_key')->label('reCAPTCHA Site Key'),
                                        Forms\Components\TextInput::make('security_recaptcha_secret')->label('reCAPTCHA Secret')->password(),
                                        Forms\Components\Toggle::make('security_turnstile_enabled')->label('Cloudflare Turnstile'),
                                        Forms\Components\TextInput::make('security_turnstile_key')->label('Turnstile Site Key'),
                                        Forms\Components\TextInput::make('security_turnstile_secret')->label('Turnstile Secret')->password(),
                                    ])->columns(3),

                                Forms\Components\Section::make('Policies & Access Controls')
                                    ->schema([
                                        Forms\Components\Toggle::make('security_2fa_required')->label('Enforce Two-Factor Authentication (2FA)'),
                                        Forms\Components\Toggle::make('security_login_alerts')->label('Login Attempt Email Alerts')->default(true),
                                        Forms\Components\Textarea::make('security_ip_blacklist')
                                            ->label('Blocked IP Addresses List')
                                            ->placeholder('e.g. 192.168.1.1, 10.0.0.1 (comma separated)'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Multi-Language')
                            ->schema([
                                Forms\Components\Section::make('Localization & RTL Settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('rtl_support')->label('RTL Mode Enabled (Arabic/Urdu)'),
                                        Forms\Components\Toggle::make('currency_switcher_enabled')->label('Show Currency Selector Dropdown'),
                                        Forms\Components\Toggle::make('country_switcher_enabled')->label('Show Country Selector Modal'),
                                        Forms\Components\Select::make('default_currency_icon')
                                            ->options([
                                                'PKR' => 'PKR (Rs.)',
                                                'USD' => 'USD ($)',
                                                'EUR' => 'EUR (€)',
                                                'AED' => 'AED (Dh)',
                                            ])->default('PKR'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Integrations & Custom Code')
                            ->schema([
                                Forms\Components\TextInput::make('google_analytics_id')
                                    ->placeholder('G-XXXXXXXXXX'),
                                Forms\Components\TextInput::make('google_tag_manager_id')
                                    ->placeholder('GTM-XXXXXXX'),
                                Forms\Components\TextInput::make('facebook_pixel_id')
                                    ->placeholder('Pixel ID'),
                                Forms\Components\Textarea::make('custom_css')
                                    ->placeholder('/* Add custom override CSS here */'),
                                Forms\Components\Textarea::make('custom_js')
                                    ->placeholder('/* Add tracking tags or pixel code here */'),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Footer Configuration')
                            ->schema([
                                Forms\Components\TextInput::make('footer_copyright')
                                    ->label('Footer Copyright Notice')
                                    ->default('© 2026 AURA Commerce. All Rights Reserved.')
                                    ->nullable(),
                                Forms\Components\Textarea::make('footer_about_text')
                                    ->label('Footer About Description')
                                    ->default('AURA is a premium high-fidelity enterprise eCommerce suite.')
                                    ->nullable(),
                                Forms\Components\TextInput::make('footer_col1_title')
                                    ->label('Column 1 Title')
                                    ->default('ONLINE SHOPPING')
                                    ->nullable(),
                                Forms\Components\Textarea::make('footer_col1_links')
                                    ->label('Column 1 Links (One item per line)')
                                    ->default("Men\nWomen\nKids\nHome & Living\nBeauty\nGenz")
                                    ->rows(5)
                                    ->nullable(),
                                Forms\Components\TextInput::make('footer_col2_title')
                                    ->label('Column 2 Title')
                                    ->default('CUSTOMER POLICIES')
                                    ->nullable(),
                                Forms\Components\Textarea::make('footer_col2_links')
                                    ->label('Column 2 Links (One item per line)')
                                    ->default("Contact Us\nFAQ\nT&C\nTrack Orders\nShipping\nPrivacy Policy")
                                    ->rows(5)
                                    ->nullable(),
                                Forms\Components\Textarea::make('footer_address')
                                    ->label('Registered Office Address')
                                    ->default('Gulberg, Lahore, Pakistan')
                                    ->nullable(),
                                Forms\Components\TextInput::make('footer_phone')
                                    ->label('Support Telephone')
                                    ->default('0300-1234567')
                                    ->nullable(),
                                Forms\Components\TextInput::make('footer_email')
                                    ->label('Support Email')
                                    ->default('support@aura.com')
                                    ->nullable(),
                                Forms\Components\Textarea::make('footer_popular_searches')
                                    ->label('Popular Searches Block')
                                    ->default('Makeup | Dresses For Girls | T-Shirts | Sandals | Bags | Sport Shoes')
                                    ->nullable(),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('CMS & Legal Policies')
                            ->schema([
                                Forms\Components\MarkdownEditor::make('page_about_us')
                                    ->label('About Us Page Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_terms_conditions')
                                    ->label('Terms & Conditions Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_privacy_policy')
                                    ->label('Privacy Policy Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_shipping_policy')
                                    ->label('Shipping Policy Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_return_policy')
                                    ->label('Return Policy Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_refund_policy')
                                    ->label('Refund Policy Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_cookie_policy')
                                    ->label('Cookie Policy Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_cancellation_policy')
                                    ->label('Cancellation Policy Content')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_careers')
                                    ->label('Careers Page Info & Openings')
                                    ->nullable(),
                                Forms\Components\MarkdownEditor::make('page_contact_info')
                                    ->label('Contact Us Info & Coordinates')
                                    ->nullable(),
                            ])->columns(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $input = $this->form->getState();

        foreach ($input as $key => $value) {
            $val = is_array($value) ? json_encode($value) : $value;
            if ($key === 'mail_password') {
                $val = !empty($value) ? encrypt($value) : '';
            }
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }

        // Set active homepage layout on layout table if it has changed
        if (isset($input['active_homepage_layout'])) {
            HomepageLayout::where('is_active', true)->update(['is_active' => false]);
            HomepageLayout::where('id', $input['active_homepage_layout'])->update(['is_active' => true]);
        }

        Cache::flush();

        Notification::make()
            ->title('Settings Saved Successfully!')
            ->success()
            ->send();
    }
}
