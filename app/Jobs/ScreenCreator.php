<?php

namespace App\Jobs;

use App\Services\screen\ScreenCreatorProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScreenCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    private array $inputPost;
    public function __construct(array $inputPost)
    {
        $this->inputPost = $inputPost;
    }

    /**
     * Execute the job.
     */
    public function handle(ScreenCreatorProcessor $processor): void
    {
       $processor->process($this->inputPost);
    }
}
