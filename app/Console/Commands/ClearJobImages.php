<?php

namespace App\Console\Commands;

use App\JobImage;
use Illuminate\Console\Command;

class ClearJobImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:job:images';

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
        if ($handle = opendir(storage_path('app/public/jobs'))) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if(!JobImage::where('file',$entry)->first()) {
                        unlink(storage_path('app/public/jobs/'.$entry));
                    }
                }
            }
            closedir($handle);
        }
    }
}
