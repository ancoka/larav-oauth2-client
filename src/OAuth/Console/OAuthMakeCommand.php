<?php
namespace Ancoka\OAuth\Console;

use Illuminate\Console\Command;

class OAuthMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:oauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold OAuth login routes';

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
        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/stubs/make/routes.stub'),
            FILE_APPEND
        );

        $this->info('OAuth scaffolding generated successfully.');
    }
}
