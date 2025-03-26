<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">System Services</h1>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="py-4">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between mb-4">
                        <div class="w-1/3">
                            <input id="service-search" type="text" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                placeholder="Search services...">
                        </div>
                        <div>
                            <button id="refresh-services" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Service Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Load
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="services-list" class="bg-white divide-y divide-gray-200">
                            <?php foreach ($services as $service): ?>
                            <tr class="service-row" data-service="<?= $service['name'] ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?= base_url('dashboard/services/' . $service['name']) ?>" class="text-indigo-600 hover:text-indigo-900">
                                        <?= $service['name'] ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= ($service['active'] === 'active') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $service['active'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $service['load'] ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                                    <?= $service['description'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if ($service['active'] === 'active'): ?>
                                    <button data-service="<?= $service['name'] ?>" data-action="stop" class="service-action text-red-600 hover:text-red-900 mr-3">
                                        Stop
                                    </button>
                                    <button data-service="<?= $service['name'] ?>" data-action="restart" class="service-action text-yellow-600 hover:text-yellow-900">
                                        Restart
                                    </button>
                                    <?php else: ?>
                                    <button data-service="<?= $service['name'] ?>" data-action="start" class="service-action text-green-600 hover:text-green-900">
                                        Start
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // WebSocket connection
        const ws = new WebSocket(`ws://${window.location.hostname}:<?= $websocketPort ?>`);
        
        ws.onopen = function() {
            console.log('WebSocket connection established');
            // Request services list
            ws.send(JSON.stringify({ action: 'get_services' }));
        };
        
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            
            console.log('Received message:', data);

            // Handle service list updates
            if (data.action === 'service_list' || data.action === 'service_list_update') {
                updateServicesList(data.services);
            }
            
            // Handle service control result
            if (data.action === 'service_control_result') {
                alert(`${data.command} ${data.service}: ${data.success ? 'Success' : 'Failed'}`);
            }
        };
        
        // Service action buttons (start, stop, restart)
        document.querySelectorAll('.service-action').forEach(button => {
            button.addEventListener('click', function() {
                const service = this.getAttribute('data-service');
                const action = this.getAttribute('data-action');
                
                if (confirm(`Are you sure you want to ${action} ${service}?`)) {
                    ws.send(JSON.stringify({
                        action: 'control_service',
                        service: service,
                        command: action
                    }));
                }
            });
        });
        
        // Refresh button
        document.getElementById('refresh-services').addEventListener('click', function() {
            ws.send(JSON.stringify({ action: 'get_services' }));
        });
        
        // Search functionality
        document.getElementById('service-search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.service-row').forEach(row => {
                const serviceName = row.getAttribute('data-service').toLowerCase();
                if (serviceName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Function to update services list
        function updateServicesList(services) {
            const tbody = document.getElementById('services-list');
            tbody.innerHTML = '';
            
            services.forEach(service => {
                const row = document.createElement('tr');
                row.className = 'service-row';
                row.setAttribute('data-service', service.name);
                
                const isActive = service.active === 'active';
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="${window.location.pathname}/${service.name}" class="text-indigo-600 hover:text-indigo-900">
                            ${service.name}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${service.active}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${service.load}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                        ${service.description}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        ${isActive ? 
                          `<button data-service="${service.name}" data-action="stop" class="service-action text-red-600 hover:text-red-900 mr-3">Stop</button>
                           <button data-service="${service.name}" data-action="restart" class="service-action text-yellow-600 hover:text-yellow-900">Restart</button>` :
                          `<button data-service="${service.name}" data-action="start" class="service-action text-green-600 hover:text-green-900">Start</button>`
                        }
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Reattach event listeners
            document.querySelectorAll('.service-action').forEach(button => {
                button.addEventListener('click', function() {
                    const service = this.getAttribute('data-service');
                    const action = this.getAttribute('data-action');
                    
                    if (confirm(`Are you sure you want to ${action} ${service}?`)) {
                        ws.send(JSON.stringify({
                            action: 'control_service',
                            service: service,
                            command: action
                        }));
                    }
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
