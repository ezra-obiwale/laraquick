<?php

namespace Laraquick\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Call a function asynchronously. 
 */
class AsyncCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $callable;
    protected $args;

    /**
     * Create a new job instance.
     *
	 * @param mixed $callable The function to call @see call_user_func_array()
	 * @param array $args The arguments to pass to the called function
     * @return void
     */
    public function __construct($callable, array $args = [])
    {
        $this->callable = $callable;
        $this->args = $args;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        call_user_func_array($this->callable, $this->args);
    }
}
