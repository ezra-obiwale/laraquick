<?php

namespace Laraquick\Controllers\Traits;

use Ratchet\ConnectionInterface;
use Laraquick\Helpers\WebSocket as HWebSocket;
use Exception;
use SplObjectStorage;

trait WebSocket {

    final public function onOpen(ConnectionInterface $conn)
    {
        HWebSocket::addClient($conn);
    }

    final public function onClose(ConnectionInterface $conn)
    {
        HWebSocket::removeClient($conn);
    }

    final public function onError(ConnectionInterface $conn, Exception $e)
    {
        $conn->close();
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
        static::onEvent($msg['event'], @$msg['data']);
        HWebSocket::resolve($msg['event'], @$msg['data']);
    }

    final protected function emit($event, $data = null)
    {
        HWebSocket::emit($event, $data);
    }

    final protected function on($event, callable $callback) {
        HWebSocket::on($event, $callback);
    }

    final protected function off($event = null, $callback = null)
    {
        HWebSocket::off($event, $callback);
    }

    protected function onEvent($event, $data = null)
    {
        
    }

}