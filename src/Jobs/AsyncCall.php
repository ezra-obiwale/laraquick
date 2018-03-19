<?php

namespace Laraquick\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

/**
 * Call a function asynchronously. 
 */
class AsyncCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $callable;
    protected $args;
    protected $failedCallable;
    protected $failedCallableArgs;

    /**
     * Create a new job instance
     *
     * @param callable|array $callable The function to call. @see call_user_func_array()
     * @param array $args The arguments for the callable
     * @param callable|array $failedCallable The function to call with the thrown exception if the job fails. @see call_user_func_array()
     * @param array $faildCallableArgs Arguments to pass to the failedCallable, along with the thrown exception
     * 
     * @return void
     */
    public function __construct($callable, array $args = [], $failedCallable = null, $faildCallableArgs = [])
    {
        $this->callable = $callable;
        $this->args = $args;
        $this->failedCallable = $failedCallable;
        $this->failedCallableArgs = $faildCallableArgs;
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

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $ex)
    {
        if ($this->failedCallable) {
            array_unshift($this->failedCallableArgs, $ex);
            call_user_func_array($this->failedCallable, $this->failedCallableArgs);
        }
    }
}