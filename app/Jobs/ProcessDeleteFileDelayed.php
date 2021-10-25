<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessDeleteFileDelayed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path;
    private $isDirectory;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $isDirectory = false)
    {
        $this->path = $path;
        $this->isDirectory = $isDirectory;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isDirectory)
            Storage::deleteDirectory($this->path);
        else
            Storage::delete($this->path);
    }
}
