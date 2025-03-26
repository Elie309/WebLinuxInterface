<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Services\SystemMonitor;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $systemMonitor;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->systemMonitor = new SystemMonitor();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Add client to collection
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        
        // Send initial system metrics
        $metrics = $this->systemMonitor->getAllMetrics();
        $conn->send(json_encode($metrics));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        
        // If client requests an update
        if (isset($data->action) && $data->action === 'get_metrics') {
            $metrics = $this->systemMonitor->getAllMetrics();
            $from->send(json_encode($metrics));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove client from collection
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Broadcast system metrics to all connected clients
     */
    public function broadcastMetrics()
    {
        $metrics = $this->systemMonitor->getAllMetrics();
        $jsonMetrics = json_encode($metrics);
        
        foreach ($this->clients as $client) {
            $client->send($jsonMetrics);
        }
    }
}
