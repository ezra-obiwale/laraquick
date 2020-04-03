<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use InvalidArgumentException;

trait Api
{

    use Common;

    /**
     * The resource to test. Used in creating the test response
     *
     * @var string
     */
    protected $resource;

    /**
     * Methods to test
     *
     * @var array
     */
    protected $methods = ['index', 'store', 'update', 'show', 'destroy'];

    /**
     * The number of models to create for the index endpoint
     *
     * @var integer
     */
    protected $indexModelCount = 5;

    /**
     * The paths to store each method's response to. See @method void storeResponse()
     *
     * @var array
     */
    protected $storePaths = [];

    /**
     * Indicates whether to store responses or not
     *
     * @var boolean
     */
    protected $storeResponses = true;

    /**
     * The payload to create/update with
     *
     * @param boolean $updating
     * @return array
     */
    abstract protected function payload($updating = false): array;

    /**
     * The index url from which all other endpoints are generated
     *
     * @return string
     */
    abstract protected function indexUrl(): string;

    /**
     * Creates a model for testing
     *
     * @param array $attributes
     * @return Model
     */
    abstract protected function createModel(array $attributes = []): Model;

    /**
     * Creates the url to use for each method
     *
     * @param string $method One of the methods in the $methods property.
     * @param integer $id The id of the target model
     *
     * @return string
     */
    protected function createUrl($method, $id = null): string
    {
        if (!in_array($method, $this->methods)) {
            throw new InvalidArgumentException('Unknown method [' . $method . ']');
        }

        $url = $this->indexUrl();

        if (!in_array($method, ['index', 'store'])) {
            $url .= '/' . $id;
        }

        return $url;
    }

    /**
     * Called before sending the index request
     *
     * @return void
     */
    protected function beforeIndex(): void
    {
        $count = 0;
        $totalModels = $this->indexModelCount;

        while ($count < $totalModels) {
            $this->createModel();
            $count++;
        }
    }

    /**
     * Called before sending the store request
     *
     * @param array $data
     * @return void
     */
    protected function beforeStore(array &$data): void
    {
    }

    /**
     * Called before sending the update request
     *
     * @param array $data
     * @param Model $model
     * @return void
     */
    protected function beforeUpdate(array &$data, Model $model): void
    {
    }

    /**
     * Called before calling the show request
     *
     * @param Model $model
     * @return void
     */
    protected function beforeShow(Model $model): void
    {
    }

    /**
     * Called before callign the destroy request
     *
     * @param Model $model
     * @return void
     */
    protected function beforeDestroy(Model $model): void
    {
    }

    /**
     * Called after all index test assertions pass
     *
     * @param TestResponse $response
     * @return void
     */
    protected function assertIndex(TestResponse $response): void
    {
    }

    /**
     * Called after all store test assertions pass
     *
     * @param TestResponse $response
     * @return void
     */
    protected function assertStore(TestResponse $response): void
    {
    }

    /**
     * Called after all update test assertions pass
     *
     * @param TestResponse $response
     * @param Model $model
     * @return void
     */
    protected function assertUpdate(TestResponse $response, Model $model): void
    {
    }

    /**
     * Called after all show test assertions pass
     *
     * @param TestResponse $response
     * @return void
     */
    protected function assertShow(TestResponse $response): void
    {
    }

    /**
     * Called after all destroy test assertions pass
     *
     * @param TestResponse $response
     * @return void
     */
    protected function assertDestroy(TestResponse $response): void
    {
    }

    public function testStore()
    {
        if (!$this->canTest('store')) {
            return $this->assertTrue(true);
        }

        $payload = $this->payload();
        $this->beforeStore($payload);

        $response = $this->post($this->createUrl('store'), $payload);

        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['store'] ?? $this->resource() . '/store');
        }

        $response->assertStatus(201);

        $this->assertStore($response);
    }

    public function testIndex()
    {
        if (!$this->canTest('index')) {
            return $this->assertTrue(true);
        }

        $this->beforeIndex();
        $response = $this->get($this->createUrl('index'));

        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['index'] ?? $this->resource() . '/index');
        }

        $response->assertStatus(200);

        $this->assertIndex($response);
    }

    public function testShow()
    {
        if (!$this->canTest('show')) {
            return $this->assertTrue(true);
        }

        $model = $this->createModel();
        $this->beforeShow($model);

        $response = $this->get($this->createUrl('show', $model->id));

        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['show'] ?? $this->resource() . '/show');
        }

        $response->assertStatus(200);

        $this->assertShow($response);
    }

    public function testUpdate()
    {
        if (!$this->canTest('update')) {
            return $this->assertTrue(true);
        }

        $payload = $this->payload(true);
        $model = $this->createModel();

        $this->beforeUpdate($payload, $model);

        $response = $this->put($this->createUrl('update', $model->id), $payload);

        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['update'] ?? $this->resource() . '/update');
        }

        $model->refresh();

        $response->assertStatus(202);

        foreach ($payload as $attr => $val) {
            $this->assertEquals($model->$attr, $val);
        }

        $this->assertUpdate($response, $model);
    }

    public function testDestroy()
    {
        if (!$this->canTest('destroy')) {
            return $this->assertTrue(true);
        }

        $model = $this->createModel();
        $this->beforeDestroy($model);

        $response = $this->delete($this->createUrl('destroy', $model->id));

        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['destroy'] ?? $this->resource() . '/destroy');
        }

        $response->assertStatus(202);

        $model = $model->fresh();

        if ($model && method_exists($model, 'trashed')) {
            $this->assertTrue($model->trashed());
        } else {
            $this->assertNull($model);
        }

        $this->assertDestroy($response);
    }

    private function canTest($method)
    {
        return in_array($method, $this->methods);
    }

    private function resource()
    {
        if (!$this->resource) {
            $filename = basename(str_replace('\\', '/', get_called_class()));
            $filename = Str::beforeLast($filename, 'Test');
            $this->resource = (Str::kebab($filename));
        }

        return $this->resource;
    }
}
