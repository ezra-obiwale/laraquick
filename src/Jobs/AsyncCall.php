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
    protected $arguments;
    protected $callback;
    protected $tags;

    /**
     * Create a new job instance
     *
     * @param callable|array $callable The function to call. @see call_user_func_array()
     * @param array $arguments The arguments to the callable. @see call_user_fun_array()
     * @param callable|array $callback The function to call with the result of the callable. In case of an exception, the second parameter will be the exception object. @see call_user_func_array()
     * @param array $tags The tags to attach to the job
     *
     * @return void
     */
    public function __construct(callable $callable, array $arguments = [], callable $callback = null, array $tag = [])
    {
        $this->callable = $callable;
        $this->arguments = $arguments;
        $this->callback = $callback;
        array_unshift($tags, 'async-call');
        $this->tags = $tags;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = call_user_func_array($this->callable, $this->arguments);
        if ($this->callback) {
            call_user_func($this->callback, $result);
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
            call_user_func($this->callback, $result, $ex);
        }
    }

    public function tags()
    {
        return $this->tags;
    }
}
