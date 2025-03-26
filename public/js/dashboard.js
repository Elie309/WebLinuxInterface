document.addEventListener('DOMContentLoaded', function() {
    // Get WebSocket port from the data attribute
    const wsPort = document.getElementById('dashboard-metrics').getAttribute('data-ws-port');
    
    // Create WebSocket connection
    const protocol = window.location.protocol === 'http:' ? 'ws:' : 'ws:';
    const wsUrl = `${protocol}//${window.location.hostname}:${wsPort}`;
    const socket = new WebSocket(wsUrl);
    
    // Connection opened
    socket.addEventListener('open', function(event) {
        console.log('Connected to WebSocket server');
        // Request initial metrics
        socket.send(JSON.stringify({action: 'get_metrics'}));
    });
    
    // Listen for messages
    socket.addEventListener('message', function(event) {
        const metrics = JSON.parse(event.data);
        updateDashboardMetrics(metrics);
    });
    
    // Handle errors
    socket.addEventListener('error', function(event) {
        console.error('WebSocket error:', event);
        document.getElementById('connection-status').innerText = 'Disconnected';
        document.getElementById('connection-status').classList.add('bg-red-100', 'text-red-800');
        document.getElementById('connection-status').classList.remove('bg-green-100', 'text-green-800');
    });
    
    // Handle connection close
    socket.addEventListener('close', function(event) {
        console.log('Disconnected from WebSocket server');
        document.getElementById('connection-status').innerText = 'Disconnected';
        document.getElementById('connection-status').classList.add('bg-red-100', 'text-red-800');
        document.getElementById('connection-status').classList.remove('bg-green-100', 'text-green-800');
        
        // Try to reconnect after 5 seconds
        setTimeout(function() {
            window.location.reload();
        }, 5000);
    });
    
    // Update dashboard with received metrics
    function updateDashboardMetrics(metrics) {
        // Update CPU usage
        document.getElementById('cpu-usage').innerText = metrics.cpu + '%';
        document.getElementById('cpu-progress').style.width = metrics.cpu + '%';
        
        // Update memory usage
        document.getElementById('memory-usage').innerText = `${metrics.memory.used} / ${metrics.memory.total}`;
        document.getElementById('memory-percent').innerText = metrics.memory.percent_used + '%';
        document.getElementById('memory-progress').style.width = metrics.memory.percent_used + '%';
        
        // Update disk usage
        // Ensure we're working with a number for the disk percentage
        const diskPercent = typeof metrics.disk.percent_used === 'number' 
            ? metrics.disk.percent_used 
            : parseFloat(metrics.disk.percent_used);
        
        document.getElementById('disk-usage').innerText = diskPercent + '%';
        document.getElementById('disk-details').innerText = `${metrics.disk.used} / ${metrics.disk.total}`;
        document.getElementById('disk-progress').style.width = diskPercent + '%';
        
        // Log metrics for debugging
        console.log('Received metrics:', metrics);
        
        // Update connection status
        document.getElementById('connection-status').innerText = 'Connected';
        document.getElementById('connection-status').classList.add('bg-green-100', 'text-green-800');
        document.getElementById('connection-status').classList.remove('bg-red-100', 'text-red-800');
    }
    
    // Request metrics every 10 seconds as a fallback
    setInterval(function() {
        if (socket.readyState === WebSocket.OPEN) {
            socket.send(JSON.stringify({action: 'get_metrics'}));
        }
    }, 10000);
});
