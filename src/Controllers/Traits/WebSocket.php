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

    /**
     * Called when a connection is opened
     *
     * @param ConnectionInterface $conn
     * @return void
     */
    final public function onOpen(ConnectionInterface $conn)
    {
        $this->connected($conn);
        HWebSocket::addClient($conn);
    }

    /**
     * Called when a connection is closed
     *
     * @param ConnectionInterface $conn
     * @return void
     */
    final public function onClose(ConnectionInterface $conn)
    {
        HWebSocket::removeClient($conn);
        $this->disconnected($conn);
    }

    /**
     * Called when an error occurs
     *
     * @param ConnectionInterface $conn
     * @param Exception $e
     * @return void
     */
    final public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->catchErrors($e, $conn);
        $conn->close();
    }

    /**
     * Called when an event is received
     *
     * @param ConnectionInterface $from
     * @param string $msg
     * @return void
     */
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

    /**
     * Emits an event to all clients
     *
     * @param string $event
     * @param mixed $data
     * @param boolean $toSelf Indicateswhether to emit event to the sender as well
     * @return void
     */
    final protected function emit($event, $data = null, $toSelf = false)
    {
        HWebSocket::emit($event, $data, $toSelf);
    }

    /**
     * Emits an event to a particular client only
     *
     * @param ConnectionInterface $client
     * @param string $event
     * @param mixed $data
     * @return void
     */
    final protected function emitTo(ConnectionInterface $client, $event, $data = null)
    {
        HWebSocket::emitTo($client, $event, $data);
    }

    final protected function on($event, callable $callback) {
        HWebSocket::on($event, $callback);
    }

    protected function shouldReceiveEvent($client, $event, $data)
    {
        return true;
    }

    /**
     * Subscribe a client to an event
     *
     * @param ConnectionInterface $client
     * @param string $to
     * @return void
     */
    private function subscribe(ConnectionInterface $client, $to)
    {
        $subs = $client->subscriptions;
        if (in_array($to, $subs['on'])) {
            return;
        }
        $subs['on'][] = $to;
        $client->subscriptions = $subs;
    }

    /**
     * Unsubscribe a client from an event
     *
     * @param ConnectionInterface $client
     * @param string $from
     * @return void
     */
    private function unsubscribe(ConnectionInterface $client, $from)
    {
        $subs = $client->subscriptions;
        if (($key = array_search($from, $subs['on'])) !== false) {
            unset($subs['on'][$key]);
        } elseif (in_array($from, $subs['off'])) {
            $subs['off'][] = $from;
        }
        $client->subscriptions = $subs;
    }

    /**
     * Checks if a client is subscribed to an event
     *
     * @param string $event
     * @param ConnectionInterface $client
     * @return boolean
     */
    private function isSubscribedTo($event, ConnectionInterface $client)
    {
        return in_array($event, $client->subscriptions['on']) 
            && !in_array($event, $client->subscriptions['off']);
    }

    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    public static function generateRandomString($length = 20, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    /**
     * Generate Random Number
     *
     * @param integer $length
     * @return void
     */
    public static function generateRandomNumber($length = 20) {
        $lower = str_repeat('1', $length);
        $upper = str_repeat('9', $length);
        return mt_rand($lower, $upper);
    }

    protected function onEvent($event, $data, $client) {}
    protected function connected($client) {}
    protected function disconnected($client) {}
    protected function catchErrors($exception, $client) {}

}