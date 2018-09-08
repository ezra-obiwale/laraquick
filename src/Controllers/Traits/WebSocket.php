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
        $event = trim($msg['event']);
        static::onEvent($event, @$msg['data']);
        $method = $this->resolveEvent($event);
        if (method_exists($this, $method)) {
            $this->$method(@$msg['data']);
        }
    }

    protected function resolveEvent($event) {
        return 'on' . ucfirst($event);
    }

    final protected function emit($event, $data = null)
    {
        HWebSocket::emit($event, $data);
    }

}