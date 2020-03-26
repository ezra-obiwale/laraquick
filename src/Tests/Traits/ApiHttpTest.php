<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Laraquick\Models\Dud;
use Str;

trait ApiHttpTest
{
    use Common;

    protected $resource = null;
    protected $methods = ['store', 'index', 'show', 'update', 'destroy'];
    protected $storePaths = [];
    protected $withHeaders = true;
    protected $storeResponses = true;

    private function apiRequest($method, $url, array $data = [])
    {
        return $this->withHeaders ? $this->request($method, $url, $data) : $this->$method($url, $data);
    }

    private function resource()
    {
        return $this->resource ?? Str::replace(['\\', '_test'], ['_', ''], strtolower(get_called_class()));
    }

    abstract protected function payload($updating = false) : array;
    abstract protected function indexUrl() : string;

    protected function beforeIndex()
    {
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

    protected function expectedIndexResponse(array &$response)
    {
        if (count($response['data'])) {
            $resp = [
                'data' => $response['data'][0]
            ];
            $this->expectedStoreResponse($resp);
            $response['data'][0] = $resp['data'];
        }
    }

    protected function expectedStoreResponse(array &$response)
    {
    }

    protected function expectedUpdateResponse(array &$response)
    {
    }

    protected function expectedShowResponse(array &$response)
    {
        $this->expectedStoreResponse($response);
    }

    protected function expectedDestroyResponse(array &$response)
    {
        $this->expectedUpdateResponse($response);
    }


    public function testStore()
    {
        if (!in_array('store', $this->methods)) {
            return $this->assertTrue(true);
        }

        $payload = $this->payload();
        $this->beforeStore($payload);
        $response = $this->apiRequest('post', $this->indexUrl(), $payload);
        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['store'] ?? $this->resource() . '/store');
        }
        $expectedResponse = [
            'status' => 'ok',
            'data' => $payload
        ];
        $this->expectedStoreResponse($expectedResponse);
        $response->assertJson($expectedResponse)->isOK();

        return $response->json()['data'];
    }

    /**
     * @depends testStore
     *
     * @param array $resource The resource created in @see testStore()
     * @param boolean $forced Indicates to force run the test
     * @return void
     */
    public function testIndex(array $resource = null, $forced = false)
    {
        if (!$forced && (!in_array('index', $this->methods) || !in_array('store', $this->methods))) {
            return $this->assertTrue(true);
        }
        $this->beforeIndex();
        $response = $this->apiRequest('get', $this->indexUrl());
        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['index'] ?? $this->resource() . '/index');
        }

        $expectedResponse = [
            'status' => 'ok',
            'data' => [ $resource ],
            'meta' => []
        ];
        if ($forced) {
            $expectedResponse['data'] = [];
            unset($expectedResponse['meta']);
        }
        $this->expectedIndexResponse($expectedResponse);
        $response->assertJson($expectedResponse)->isOK();
    }

    /**
     * Will run if store method is not being tested but index is
     *
     * @return void
     */
    public function testForcedIndex()
    {
        if (!in_array('store', $this->methods) && in_array('index', $this->methods)) {
            $resource = $this->payload();
            $resource['id'] = 1;
            return $this->testIndex($resource, true);
        }
        $this->assertTrue(true);
    }

    /**
     * @depends testStore
     *
     * @param array $resource The resource created in @see testStore()
     * @param boolean $forced Indicates to force run the test
     * @return void
     */
    public function testShow(array $resource = null, $forced = false)
    {
        if (!$forced && (!in_array('show', $this->methods) || !in_array('store', $this->methods))) {
            return $this->assertTrue(true);
        }

        if (!$resource) {
            $resource = $this->payload();
            $resource['id'] = 1;
        }

        $model = (new Dud)->forceFill($resource);
        $this->beforeShow($model);

        $response = $this->apiRequest('get', $this->indexUrl() . '/' . $model->id);
        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['show'] ?? $this->resource() . '/show');
        }
        $expectedResponse = [
            'status' => 'ok',
            'data' => $resource
        ];
        $this->expectedShowResponse($expectedResponse);
        $response->assertJson($expectedResponse)->isOK();
    }

    /**
     * Will run if store method is not being tested but show is
     *
     * @return void
     */
    public function testForcedShow()
    {
        if (!in_array('store', $this->methods) && in_array('show', $this->methods)) {
            $resource = $this->payload();
            $resource['id'] = 1;
            return $this->testShow($resource, true);
        }
        $this->assertTrue(true);
    }

    /**
     * @depends testStore
     *
     * @param array $resource The resource created in @see testStore()
     * @param boolean $forced Indicates to force run the test
     * @return void
     */
    public function testUpdate(array $resource = null, $forced = false)
    {
        if (!$forced && (!in_array('update', $this->methods) || !in_array('store', $this->methods))) {
            return $this->assertTrue(true);
        }

        $payload = $this->payload(true);
        $model = (new Dud)->forceFill($resource);
        $this->beforeUpdate($payload, $model);

        $response = $this->apiRequest('put', $this->indexUrl() . '/' . $model->id, $payload);
        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['update'] ?? $this->resource() . '/update');
        }
        $expectedResponse = [
            'status' => 'ok',
            'data' => array_merge($resource, $payload)
        ];
        $this->expectedUpdateResponse($expectedResponse);
        $response->assertJson($expectedResponse)->isOK();

        return $response->json()['data'];
    }

    /**
     * Will run if store method is not being tested but update is
     *
     * @return void
     */
    public function testForcedUpdate()
    {
        if (!in_array('store', $this->methods) && in_array('update', $this->methods)) {
            $resource = $this->payload(true);
            $resource['id'] = 1;
            return $this->testUpdate($resource, true);
        }
        $this->assertTrue(true);
    }

    /**
     * @depends testUpdate
     *
     * @param array $resource The resource updated in @see testUpdate()
     * @param boolean $forced Indicates to force run the test
     * @return void
     */
    public function testDestroy(array $resource = null, $forced = false)
    {
        if (!$forced && (!in_array('destroy', $this->methods) || !in_array('update', $this->methods))) {
            return $this->assertTrue(true);
        }

        $model = (new Dud)->forceFill($resource);
        $this->beforeDestroy($model);

        $response = $this->apiRequest('delete', $this->indexUrl() . '/' . $model->id);
        if ($this->storeResponses) {
            $this->storeResponse($response, $this->storePaths['destroy'] ?? $this->resource() . '/destroy');
        }
        $expectedResponse = [
            'status' => 'ok',
            'data' => $resource
        ];
        $this->expectedDestroyResponse($expectedResponse);
        $response->assertJson($expectedResponse)->isOK();
    }

    /**
     * Will run if update method is not being tested but destroy is
     *
     * @return void
     */
    public function testForcedDestroy()
    {
        if (!in_array('update', $this->methods) && in_array('destroy', $this->methods)) {
            $resource = $this->payload(true);
            $resource['id'] = 1;
            return $this->testDestroy($resource, true);
        }
        $this->assertTrue(true);
    }
}
