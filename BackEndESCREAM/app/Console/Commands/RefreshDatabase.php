<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshDatabase extends Command
{
    /**
     * El nombre y la firma del comando.
     */
    protected $signature = 'db:refreshAll';

    /**
     * La descripción del comando.
     */
    protected $description = 'Run migrate:fresh, seed the database, and fetch the movie images from the TMDB API (The Movie DataBase)';

    /**
     * Ejecutar el comando.
     */
    public function handle()
    {
        $this->info('Refreshing database...');

        $this->call('migrate:fresh', [
            '--seed' => true,
        ]);

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\MovieImageSeeder',
        ]);

        $this->info('Database refreshed, seeded and fetched images!');
    }
}
