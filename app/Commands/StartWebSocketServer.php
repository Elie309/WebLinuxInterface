<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Services\WebSocketServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as SocketServer;

class StartWebSocketServer extends BaseCommand
{
    protected $group       = 'WebLinuxInterface';
    protected $name        = 'websocket:start';
    protected $description = 'Starts the WebSocket server for real-time system monitoring';
    protected $usage       = 'websocket:start [options]';
    protected $options     = [
        '--port' => 'Port to run the WebSocket server on (default: 8000)'
    ];

    public function run(array $params)
    {
        CLI::write('Starting WebSocket server...', 'green');
        
        // Check for port parameter
        $port = $params['port'] ?? CLI::getOption('port') ?? 8000;
        
        // Create event loop
        $loop = LoopFactory::create();
        
        // Create WebSocket server component
        $webSocketServer = new WebSocketServer();
        
        // Create socket server
        $socket = new SocketServer("0.0.0.0:{$port}", $loop);
        
        // Create HTTP/WebSocket server
        $server = new IoServer(
            new HttpServer(
                new WsServer($webSocketServer)
            ),
            $socket,
            $loop
        );
        
        // Add periodic timer to broadcast system metrics (every 2 seconds)
        $loop->addPeriodicTimer(2, function () use ($webSocketServer) {
            $webSocketServer->broadcastMetrics();
        });
        
        CLI::write("WebSocket server running on port {$port}", 'green');
        CLI::write('Press Ctrl+C to stop the server', 'yellow');
        
        // Start the server
        $server->run();
    }
}
