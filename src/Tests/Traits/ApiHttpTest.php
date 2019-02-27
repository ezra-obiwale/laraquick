<?php

namespace Laraquick\Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Laraquick\Models\Dud;

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
        return $this->resource ?? str_replace(['\\', '_test'], ['_', ''], strtolower(get_called_class()));
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
        $resp = [
            'data' => $response['data'][0]
        ];
        $this->expectedStoreResponse($resp);
        $response['data'][0] = $resp['data'];
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
     * @param array $resource
     * @return void
     */
    public function testIndex(array $resource)
    {
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
        $this->expectedIndexResponse($expectedResponse);
        $response->assertJson($expectedResponse)->isOK();
    }

    /**
     * @depends testStore
     *
     * @param array $resource
     * @return void
     */
    public function testShow(array $resource)
    {
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
     * @depends testStore
     *
     * @param array $resource
     * @return void
     */
    public function testUpdate(array $resource)
    {
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
     * @depends testUpdate
     *
     * @param array $resource
     * @return void
     */
    public function testDestroy(array $resource)
    {
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
}
