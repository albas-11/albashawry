<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\OtoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = Setting::getInstance();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            // Informations Générales
            'site_name' => 'required|string|max:255',
            'locale' => 'required|in:fr,en,ar',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',

            // Coordonnées
            'admin_email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',

            // Identifiants légaux (registre de commerce / TVA)
            'cr_number' => 'nullable|string|max:50',
            'cr_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'vat_number' => 'nullable|string|max:50',
            'vat_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            // SMTP
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|between:1,65535',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',

            // SEO
            'seo_title' => 'required|string|max:255',
            'seo_description' => 'required|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',

            // Telr
           'tap_public_key' => 'nullable|string|max:255',
            'tap_secret_key' => 'nullable|string|max:255',

            // OTO (Tryoto)
            'oto_client_id' => 'nullable|string|max:255',
            'oto_client_secret' => 'nullable|string|max:255',
            'oto_warehouse_code' => 'nullable|string|max:255',
            'oto_warehouse_name' => 'nullable|string|max:255',
            'oto_warehouse_phone' => 'nullable|string|max:20',
            'oto_warehouse_city' => 'nullable|string|max:255',
            'oto_warehouse_address' => 'nullable|string|max:500',
            'oto_webhook_secret' => 'nullable|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ], [
            'site_name.required' => 'Le nom du site est obligatoire',
            'admin_email.required' => 'L\'email est obligatoire',
            'admin_email.email' => 'L\'email doit être valide',
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'seo_title.required' => 'Le titre SEO est obligatoire',
            'seo_description.required' => 'La description SEO est obligatoire',
            'logo.image' => 'Le logo doit être une image',
            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo',
            'favicon.image' => 'Le favicon doit être une image',
            'favicon.max' => 'Le favicon ne doit pas dépasser 1 Mo',
            'cr_image.image' => __('message.The commercial register file must be an image'),
            'cr_image.max' => __('message.The image must not exceed 2 MB'),
            'vat_image.image' => __('message.The VAT certificate file must be an image'),
            'vat_image.max' => __('message.The image must not exceed 2 MB'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $settings = Setting::getInstance();
           // $data = $request->except(['logo', 'favicon', 'smtp_password', 'telr_auth_key', 'oto_client_secret', 'oto_webhook_secret']);

$data = $request->except(['logo', 'favicon', 'cr_image', 'vat_image', '_token', '_method', 'smtp_password', 'telr_auth_key', 'tap_secret_key', 'oto_client_secret', 'oto_webhook_secret']);
            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($settings->logo && file_exists(public_path('images/settings/' . $settings->logo))) {
                    unlink(public_path('images/settings/' . $settings->logo));
                }
                $logoFile = $request->file('logo');
                $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
                $logoFile->move(public_path('images/settings'), $logoName);
                $data['logo'] = $logoName;
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                if ($settings->favicon && file_exists(public_path('images/settings/' . $settings->favicon))) {
                    unlink(public_path('images/settings/' . $settings->favicon));
                }
                $faviconFile = $request->file('favicon');
                $faviconName = 'favicon_' . time() . '.' . $faviconFile->getClientOriginalExtension();
                $faviconFile->move(public_path('images/settings'), $faviconName);
                $data['favicon'] = $faviconName;
            }

            // Handle commercial register / VAT images
            foreach (['cr_image', 'vat_image'] as $field) {
                if ($uploaded = $this->storeSettingImage($request, $field, $settings->{$field})) {
                    $data[$field] = $uploaded;
                }
            }

            // Handle passwords (only update if provided)
            if ($request->filled('smtp_password')) {
                $data['smtp_password'] = $request->smtp_password;
            }

            if ($request->filled('telr_auth_key')) {
                $data['telr_auth_key'] = $request->telr_auth_key;
            }

           if ($request->filled('tap_secret_key')) {
                $data['tap_secret_key'] = $request->tap_secret_key;
            }

            if ($request->filled('oto_client_secret')) {
                $data['oto_client_secret'] = $request->oto_client_secret;
            }

            if ($request->filled('oto_webhook_secret')) {
                $data['oto_webhook_secret'] = $request->oto_webhook_secret;
            }

            // Update settings
            $settings->update($data);

            // Update .env file for important configs
            $this->updateEnvFile([
                'APP_NAME' => $request->site_name,
                'APP_LOCALE' => $request->locale,
                'MAIL_MAILER' => 'smtp',
                'MAIL_HOST' => $request->smtp_host,
                'MAIL_PORT' => $request->smtp_port,
                'MAIL_ENCRYPTION' => $request->smtp_encryption,
                'MAIL_USERNAME' => $request->smtp_username,
                'MAIL_FROM_ADDRESS' => $request->admin_email,
            ]);

            return redirect()->back()->with('success', __('The settings have been successfully updated!'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error during update : ' . $e->getMessage()))
                ->withInput();
        }
    }


    /**
     * Enregistre une image de réglage dans public/images/settings
     * et supprime l'ancienne. Retourne le nom du fichier, ou null si rien n'a été envoyé.
     */
    protected function storeSettingImage(Request $request, string $field, ?string $currentFile): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        if ($currentFile && file_exists(public_path('images/settings/' . $currentFile))) {
            unlink(public_path('images/settings/' . $currentFile));
        }

        $file = $request->file($field);
        $name = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/settings'), $name);

        return $name;
    }

    /**
     * Update .env file
     */
    protected function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            // Skip if value is empty
            if (empty($value)) {
                continue;
            }

            // Add quotes if value contains spaces
            $value = strpos($value, ' ') !== false ? '"' . $value . '"' : $value;

            // Check if key exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);

        // Clear config cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Test OTO connection
     */
    public function testOto(): \Illuminate\Http\JsonResponse
    {
        // Flush cached token so we use the latest saved credentials
        $settings = Setting::getInstance();
        $cacheKey = 'oto_access_token_' . md5($settings->oto_client_id . $settings->oto_client_secret);
        Cache::forget($cacheKey);

        $result = app(OtoService::class)->testConnection();

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        try {
            Mail::raw('Test email from ' . config('app.name'), function ($message) use ($request) {
                $message->to($request->admin_email)
                    ->subject(__('Test Email Configuration'));
            });

            return response()->json([
                'success' => true,
                'message' => __('Test email sent successfully!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error : ' . $e->getMessage()
            ], 500);
        }
    }
}
