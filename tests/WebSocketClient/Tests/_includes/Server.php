<?php
namespace WebSocketClient\Tests;

use Closure;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Http\Router;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServer;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\StreamSelectLoop;
use React\Socket\Server as Reactor;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Server implements WampServerInterface
{
    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Reactor
     */
    private $socket;

    /**
     * @var IoServer
     */
    private $server;

    /**
     * @var Topic[]
     */
    private $subscribers = array();

    /**
     * @var callable
     */
    private $onMessageCallback;

    /**
     * @param StreamSelectLoop $loop
     * @param int $port
     * @param string $path
     */
    function __construct(StreamSelectLoop $loop, $port, $path)
    {
        $httpHost = 'localhost';

        $this->setPort($port)
            ->setPath($path)
            ->setSocket(new Reactor($loop))
            ->setRoutes(new RouteCollection);

        $this->getSocket()->listen($this->getPort());

        $this->getRoutes()
            ->add(
                'rr-1',
                new Route(
                    $this->getPath(),
                    array('_controller' => new WsServer(new WampServer($this))),
                    array('Origin' => $httpHost),
                    array(),
                    $httpHost
                )
            );

        $this->setServer(
            new IoServer(
                new HttpServer(
                    new Router(
                        new UrlMatcher($this->getRoutes(), new RequestContext)
                    )
                ),
                $this->getSocket(),
                $loop
            )
        );
    }

    /**
     * Release websocket server
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Release websocket server
     */
    public function close()
    {
        $socket = $this->getSocket();
        if (null !== $socket) {
            $this->getSocket()->shutdown();
            $this->setSocket(null);
        }
    }

    /**
     * @param string $topic
     * @param string $message
     */
    public function broadcast($topic, $message)
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->getId() === $topic) {
                $subscriber->broadcast($message);
            }
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $id
     * @param Topic|string $topic
     * @param array $params
     */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $conn->send(json_encode(array(
            3,
            $id,
            $params
        )));
    }

    /**
     * @param ConnectionInterface $conn
     * @param Topic|string $topic
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onMessage(ConnectionInterface $conn, $data)
    {
        $callback = $this->getOnMessageCallback();
        if (null !== $callback) {
            $callback($conn, $data);
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @param Exception $e
     */
    public function onError(ConnectionInterface $conn, Exception $e)
    {
    }

    /**
     * @param callable $onSubscribeCallback
     * @return self
     */
    public function setOnMessageCallback(Closure $onMessageCallback)
    {
        $this->onMessageCallback = $onMessageCallback;
        return $this;
    }

    /**
     * @return callable
     */
    public function getOnMessageCallback()
    {
        return $this->onMessageCallback;
    }

    /**
     * @param int $port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = (int)$port;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param Reactor|null $socket
     * @return self
     */
    public function setSocket(Reactor $socket = null)
    {
        $this->socket = $socket;
        return $this;
    }

    /**
     * @return Reactor
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @param RouteCollection $routes
     * @return self
     */
    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param IoServer $server
     * @return self
     */
    public function setServer(IoServer $server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return IoServer
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = (string)$path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
