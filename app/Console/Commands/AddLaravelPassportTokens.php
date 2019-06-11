<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AddLaravelPassportTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:oauth:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Laravel Passport Oauth Tokens if not exist';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::statement('TRUNCATE TABLE oauth_clients;');
        Artisan::call('passport:install');
        $this->info("Regenerated Laravel Passport Oauth tokens!");
    }
}
