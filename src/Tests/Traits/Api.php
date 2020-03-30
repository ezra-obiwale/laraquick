<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

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

    protected function beforeIndex()
    {
        $count = 0;
        $totalModels = Config::get('laraquick.tests.responses.index_models');

        while ($count < $totalModels) {
            $this->createModel();
            $count++;
        }
    }

    protected function beforeStore(array &$data)
    {
    }

    protected function beforeUpdate(array &$data, Model $model)
    {
    }

    protected function beforeShow(Model $model)
    {
    }

    protected function beforeDestroy(Model $model)
    {
    }

    protected function assertIndex(TestResponse $response)
    {
    }

    protected function assertStore(TestResponse $response)
    {
    }

    protected function assertUpdate(TestResponse $response, Model $model)
    {
    }

    protected function assertShow(TestResponse $response)
    {
    }

    protected function assertDestroy(TestResponse $response)
    {
    }

    public function testStore()
    {
        if (!$this->canTest('store')) {
            return $this->assertTrue(true);
        }

        $payload = $this->payload();
        $this->beforeStore($payload);
        $response = $this->post($this->indexUrl(), $payload);

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
        $response = $this->get($this->indexUrl());

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

        $response = $this->get($this->indexUrl() . '/' . $model->id);

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

        $response = $this->put($this->indexUrl() . '/' . $model->id, $payload);

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

        $response = $this->delete($this->indexUrl() . '/' . $model->id);

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
