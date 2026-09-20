<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OtoService;

class TestOtoConnection extends Command
{
    protected $signature = 'oto:test';
    protected $description = 'Tester la connexion avec l\'API OTO';

    public function handle()
    {
        $this->info('🔄 Test de connexion OTO...');

        $otoService = app(OtoService::class);
        $result = $otoService->testConnection();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->line('Token: ' . $result['token']);
        } else {
            $this->error('❌ ' . $result['message']);
            return 1;
        }

        return 0;
    }
}
