<?php

namespace Aerni\Snipcart\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Site;
use Stillat\Proteus\Support\Facades\ConfigWriter;

class SyncSites extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'snipcart:sync-sites';

    /**
     * The console command description.
     */
    protected $description = 'Sync the Snipcart with the Statamic sites';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->update();

        $this->line('<info>[✓]</info> Synced the sites in <comment>config/snipcart.php</comment> with the sites in <comment>config/statamic/sites.php</comment>');
    }

    /**
     * Update the Snipcart config sites array
     */
    protected function update(): void
    {
        $snipcartConfig = collect(ConfigWriter::getConfigItem('snipcart.sites'));

        $inStatamicSitesArray = $snipcartConfig->intersectByKeys($this->sites());

        $newConfig = $this->sites()
            ->merge($inStatamicSitesArray)
            ->toArray();

        ConfigWriter::edit('snipcart')
            ->replace('sites', $newConfig)
            ->save();
    }

    /**
     * Get a site config for each Statamic site
     */
    protected function sites(): Collection
    {
        return Site::all()->mapWithKeys(fn ($site) => [
            $site->handle() => [
                'currency' => 'USD',
                'length' => 'in',
                'weight' => 'oz',
            ],
        ]);
    }
}
