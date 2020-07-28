<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Content;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;

class SetupSnipcart extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipcart:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Snipcart';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $content = new Content();

        $content->setup();

        foreach ($content->messages() as $message) {
            $this->info($message);
        }
    }
}
