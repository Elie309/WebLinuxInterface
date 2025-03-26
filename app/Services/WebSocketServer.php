<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Services\SystemMonitor;
use App\Services\ServicesManager;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $systemMonitor;
    protected $servicesManager;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->systemMonitor = new SystemMonitor();
        $this->servicesManager = new ServicesManager();
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
        
        // Process based on action type
        if (isset($data->action)) {
            switch ($data->action) {
                case 'get_metrics':
                    $metrics = $this->systemMonitor->getAllMetrics();
                    $from->send(json_encode($metrics));
                    break;
                    
                case 'get_services':
                    $services = $this->servicesManager->getServices();
                    $from->send(json_encode([
                        'action' => 'service_list',
                        'services' => $services
                    ]));
                    break;
                    
                case 'get_service_details':
                    if (isset($data->service)) {
                        $details = $this->servicesManager->getServiceDetails($data->service);
                        $from->send(json_encode([
                            'action' => 'service_details',
                            'service' => $data->service,
                            'details' => $details
                        ]));
                    }
                    break;
                    
                case 'control_service':
                    if (isset($data->service) && isset($data->command)) {
                        $success = $this->servicesManager->controlService($data->service, $data->command);
                        
                        // Get updated service details after command
                        $details = $this->servicesManager->getServiceDetails($data->service);
                        
                        $from->send(json_encode([
                            'action' => 'service_control_result',
                            'service' => $data->service,
                            'command' => $data->command,
                            'success' => $success,
                            'details' => $details
                        ]));
                        
                        // Broadcast updated service list to all clients
                        $this->broadcastServiceUpdate();
                    }
                    break;
            }
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
    
    /**
     * Broadcast service update to all connected clients
     */
    public function broadcastServiceUpdate()
    {
        $services = $this->servicesManager->getServices();
        $jsonData = json_encode([
            'action' => 'service_list_update',
            'services' => $services
        ]);
        
        foreach ($this->clients as $client) {
            $client->send($jsonData);
        }
    }
}
