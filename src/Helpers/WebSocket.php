<?php

namespace Laraquick\Helpers;

use SplObjectStorage;
use Ratchet\ConnectionInterface;

class WebSocket
{
    private static $callbacks = [];
    private static $clients;
    private static $currentClient;
    private static $shouldReceiveEvent;

    private static function init()
    {
        if (!self::$clients) {
            self::$clients = new SplObjectStorage;
        }
        if (!self::$shouldReceiveEvent) {
            self::$shouldReceiveEvent = function () {
                return true;
            };
        }
    }

    public static function canReceiveEvent(callable $should)
    {
        self::$shouldReceiveEvent = $should;
    }

    public static function addClient(ConnectionInterface $client)
    {
        self::init();
        self::$clients->attach($client);
    }

    public static function removeClient(ConnectionInterface $client)
    {
        self::init();
        self::$clients->detach($client);
    }

    public static function setCurrentClient(ConnectionInterface $client)
    {
        self::$currentClient = $client;
    }

    public static function emit($event, $data = null, $toSelf = false)
    {
        foreach (self::$clients as $client) {
            if ((!$toSelf && $client == self::$currentClient) ||
                !call_user_func(self::$shouldReceiveEvent, $client, $event, $data)) {
                continue;
            }
            self::emitTo($client, $event, $data);
        }
    }

    public static function emitTo (ConnectionInterface $client, $event, $data = null)
    {
        $client->send(json_encode([
            'event' => trim($event),
            'data' => $data
        ]));
    }

    public static function on($event, callable $callback)
    {
        self::$callbacks[$event][] = $callback;
    }

    public static function off($event = null, callable $callback = null)
    {
        if (!$event) {
            self::$callbacks = [];
        } elseif (!$callback) {
            self::$callbacks[$event] = [];
        } else {
            foreach (self::$callbacks as $key => $cb) {
                if ($callback == $cb) {
                    unset(self::$callbacks[$key]);
                }
            }
        }
    }

    public static function resolve($event, $data, ConnectionInterface $from)
    {
        if (!array_key_exists($event, self::$callbacks)) {
            return;
        }
        foreach (self::$callbacks[$event] as $callback) {
            $callback($data, $from);
        }
    }
}
