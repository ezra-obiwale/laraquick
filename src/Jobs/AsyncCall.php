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

    public function __construct(
        protected string $className,
        protected string $methodName,
        protected array $params = [],
        protected array $tags = []
    ) {
        array_unshift($this->tags, 'async-call');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $object = resolve($this->className);

        call_user_func_array([$object, $this->methodName], $this->params);
    }

    /**
     * Sets the tags for the job
     *
     * @return string|array
     */
    public function tags()
    {
        return $this->tags;
    }
}
