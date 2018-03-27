<?php
namespace Laraquick\Controllers\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laraquick\Helpers\Http;
use Laraquick\Models\Dud;

/**
 * A collection of methods to assist in quick controller generation
 * to pass CRUD request to another server
 *
 */
trait PassThrough
{
    use Api;

    protected $responseStatusCode;

    public function model()
    {}

    /**
     * The headers to pass into requests
     *
     * @return array
     */
    abstract protected function headers();
    
    /**
     * The url to pass the request to
     *
     * @return string
     */
    abstract protected function toUrl();

    /**
     * Headers to send with index request
     *
     * @return array
     */
    protected function indexHeaders()
    {
        return $this->headers();
    }

    /**
     * Headers to send with store request
     *
     * @return array
     */
    protected function storeHeaders()
    {
        return $this->headers();
    }

    /**
     * Headers to send with show request
     *
     * @return array
     */
    protected function showHeaders()
    {
        return $this->headers();
    }

    /**
     * Headers to send with update request
     *
     * @return array
     */
    protected function updateHeaders()
    {
        return $this->headers();
    }

    /**
     * Headers to send with destroy request
     *
     * @return array
     */
    protected function destroyHeaders()
    {
        return $this->headers();
    }

    /**
     * Add / to the url if not added
     *
     * @return string
     */
    private function to()
    {
        $to = self::toUrl();
        if ($to[strlen($to) - 1] !== '/')
            $to .= '/';
        return $to;
    }

    /**
     * Prepares the response to be sent
     *
     * @param string $action
     * @return Response
     */
    private function respond($action, $resp)
    {
        $this->responseStatusCode = Http::getStatusCode();

        $beforeMethod = 'before' . ucfirst($action) . 'Response';
        if (!Http::hasErrors() && $action !== 'index') {
            if (array_key_exists('data', $resp)) {
                $resp['data'] = (new Dud)->forceFill($resp['data']);
            }
            else {
                $resp = (new Dud)->forceFill($resp);
            }
        }

        if ($response = $this->$beforeMethod(array_key_exists('data', $resp) ? $resp['data'] : $resp)) return $response;
        return response()->json($resp, Http::getStatusCode());
    }

    private function request($action, $id = null, $data = null)
    {
        $headerMethod = $action . 'Headers';
        $options = [
            'query' => request()->query(),
            'headers' => $this->$headerMethod()
        ];
        if ($data) $options['json'] = $data;
        $method = $this->methodMap($action);
        $resp = $this->httpRequest($method, self::to() . $id, $options);
        return $this->respond($action, $resp);
    }

    /**
     * Shortcut for making http requests
     *
     * @param string $method GET|POST|PUT|PATCH|DELETE ...
     * @param string $url
     * @param array $options
     * @return array
     */
    protected function httpRequest($method, $url, array $options = [])
    {
        return Http::request($method, $url, $options);
    }

    /**
     * Shortcut to get the status code of the last request
     *
     * @return integer
     */
    protected function httpStatusCode()
    {
        return Http::getStatusCode();
    }

    /**
     * Shortcut to get the raw response object of the guzzle request
     *
     * @return void
     */
    protected function httpResponse()
    {
        return Http::rawResponse();
    }

    /**
     * Check wither the request has errors or not
     *
     * @return boolean
     */
    protected function hasErrors() {
        return Http::hasErrors();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return $this->request('index');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if ($resp = $this->validateRequest($data, $this->validationRules($data)))
            return $resp;

        if ($resp = $this->beforeStore($data)) return $resp;

        return $this->request('store', null, $data);
    }

    /**
     * Fetch a resource
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return $this->request('show', $id);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        if ($resp = $this->validateRequest($data, $this->validationRules($data, $id)))
            return $resp;

        if ($resp = $this->beforeUpdate($data)) return $resp;

        return $this->request('update', $id, $data);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return $this->request('destroy', $id);
    }
    
    /**
     * An array map of actions to request methods
     *
     * @param string $action The action for which map should be returned
     * @return array|string
     */
    protected function methodMap($action = null)
    {
        $map = [
            'index' => 'GET',
            'store' => 'POST',
            'show' => 'GET',
            'update' => 'PUT',
            'destroy' => 'DELETE'
        ];

        if ($action) return $map[$action];
        return $map;
    }
}