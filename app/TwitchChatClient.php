<?php
#app/TwitchChatClient.php

namespace App;

class TwitchChatClient
{
    protected $socket;
    protected $nick;
    protected $oauth;

    static $host = "irc.chat.twitch.tv";
    static $port = "6667";
    static $channel = TWITCH_CHANNEL;

    public function __construct($nick, $oauth)
    {
        $this->nick = $nick;
        $this->oauth = $oauth;
    }

    public function connect()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (socket_connect($this->socket, self::$host, self::$port) === FALSE) {
            return null;
        }

        $this->authenticate();
        $this->setNick();
        $this->joinChannel(self::$channel);
    }

    public function authenticate()
    {
        $this->send(sprintf("PASS %s", $this->oauth));
    }

    public function setNick()
    {
        $this->send(sprintf("NICK %s", $this->nick));
    }

    public function joinChannel($channel)
    {
        $this->send(sprintf("JOIN #%s", $channel));
        $this->send('PRIVMSG #'.self::$channel.' :Olá chat! O bot está na área!');
    }

    public function getLastError()
    {
        return socket_last_error($this->socket);
    }

    public function isConnected()
    {
        return !is_null($this->socket);
    }

    public function read($size = 256)
    {
        if (!$this->isConnected()) {
            return null;
        }

        return socket_read($this->socket, $size);
    }

    public function send($message)
    {
        if (!$this->isConnected()) {
            return null;
        }

        return socket_write($this->socket, $message . "\n");
    }

    public function close()
    {
        socket_close($this->socket);
    }
}
