<?php

namespace App\Console\Commands;

use App\Location;
use App\Traits\GlobalLines;
use Illuminate\Console\Command;

class UpdateLocationsToCities extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:to:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $locations = Location::all();
        foreach ($locations as $location) {
            $city = $this->_workingWithSimpleTable('App\City', ['name' => $location->name]);
        }
    }
}
