<?php

namespace Laraquick\Controllers\Traits;

use Ratchet\ConnectionInterface;
use Laraquick\Helpers\WebSocket as HWebSocket;
use Exception;
use SplObjectStorage;

trait WebSocket {

    public function __constructor() {
        parent::__constructor();

        HWebSocket::canReceiveEvent(static::shouldReceiveEvent);
    }

    final public function onOpen(ConnectionInterface $conn)
    {
        return HWebSocket::addClient($conn);
    }

    final public function onClose(ConnectionInterface $conn)
    {
        return HWebSocket::removeClient($conn);
    }

    final public function onError(ConnectionInterface $conn, Exception $e)
    {
        return $conn->close();
    }

    final public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg, true);
        if (!is_array($msg) ||
            !array_key_exists('event', $msg) ||
            !trim($msg['event'])) {
            return;
        }
        HWebSocket::setCurrentClient($from);
        static::onEvent($msg['event'], @$msg['data'], $from);
        HWebSocket::resolve($msg['event'], @$msg['data'], $from);
    }

    final protected function emit($event, $data = null, $toSelf = false)
    {
        HWebSocket::emit($event, $data, $toSelf);
    }

    final protected function on($event, callable $callback) {
        HWebSocket::on($event, $callback);
    }

    protected function shouldReceiveEvent($client, $event, $data)
    {
        return true;
    }

    protected function onEvent($event, $data, $client)
    {
        
    }

}