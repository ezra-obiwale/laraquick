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
    protected $callback;

    /**
     * Create a new job instance
     *
     * @param callable|array $callable The function to call. @see call_user_func_array()
     * @param array $args The arguments for the callable
     * @param callable|array $callback The function to call with the result of the callable. In case of an exception, the second parameter will be the exception object. @see call_user_func_array()
     * 
     * @return void
     */
    public function __construct(callable $callable, array $args = [], callable $callback = null)
    {
        $this->callable = $callable;
        $this->args = $args;
        $this->callback = $callback;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = call_user_func_array($this->callable, $this->args);
        if ($this->callback) {
            call_user_func_array($this->callback, [$result]);
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $ex)
    {
        if ($this->callback) {
            call_user_func_array($this->callback, [$result, $ex]);
        }
    }
}
