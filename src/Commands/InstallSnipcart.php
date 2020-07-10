<?php

namespace Aerni\Snipcart\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Statamic\Console\RunsInPlease;

class InstallSnipcart extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipcart:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Let Statamic Snipcart help you through the setup process.';

    public function handle()
    {
        $this->info('### Step 1 ############################## Statamic Snipcart ###');
        $this->info('### Publishing vendor files ###################################');
        $this->info('###############################################################');

        $this->confirm('Let me publish the Snipcart vendor files for you. Please confirm.');

        Artisan::call('vendor:publish',[
            '--provider' => 'Aerni\Snipcart\ServiceProvider',
            '--force' => true,
        ]);

        $this->info('Blueprint: resources/blueprints/products.php');
        $this->info('Config: config/snipcart.php');
        $this->info('Lang: resources/lang/vendor/snipcart/');
    }
}