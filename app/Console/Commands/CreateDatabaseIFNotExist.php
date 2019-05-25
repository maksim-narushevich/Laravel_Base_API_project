<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabaseIFNotExist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:database {db_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database if not exist';

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
        $db_name = $this->argument('db_name');
        DB::statement('CREATE DATABASE IF NOT EXISTS '.$db_name);
        $this->info("Database ".$db_name." was created");
    }
}
