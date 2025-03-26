<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex items-center">
            <a href="<?= base_url('dashboard/services') ?>" class="mr-4 text-indigo-600 hover:text-indigo-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Service: <?= $serviceName ?></h1>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-5">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Service Information</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Details and controls for <?= $serviceName ?> service.</p>
                </div>
                
                <div class="flex space-x-2" id="service-controls">
                    <?php if ($details['active']): ?>
                        <button id="btn-stop" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Stop
                        </button>
                        <button id="btn-restart" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Restart
                        </button>
                    <?php else: ?>
                        <button id="btn-start" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Start
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($details['enabled']): ?>
                        <button id="btn-disable" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Disable
                        </button>
                    <?php else: ?>
                        <button id="btn-enable" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Enable
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?= $serviceName ?></dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                            <span id="service-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $details['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $details['status'] ?>
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Enabled at boot</dt>
                        <dd id="service-enabled" class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <?= $details['enabled'] ? 'Yes' : 'No' ?>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Output</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div id="service-output" class="bg-gray-100 p-3 rounded-md font-mono whitespace-pre overflow-x-auto text-xs max-h-96 overflow-y-auto">
                                <?= htmlspecialchars($details['output']) ?>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceName = '<?= $serviceName ?>';
        const ws = new WebSocket(`ws://${window.location.hostname}:<?= $websocketPort ?>`);
        
        ws.onopen = function() {
            console.log('WebSocket connection established');
            // Request service details
            ws.send(JSON.stringify({ 
                action: 'get_service_details', 
                service: serviceName 
            }));
        };
        
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            
            // Handle service details update
            if (data.action === 'service_details') {
                updateServiceDetails(data.details);
            }

            console.log(data);
            
            // Handle service control result
            if (data.action === 'service_control_result' && data.service === serviceName) {
                alert(`${data.command} ${data.service}: ${data.success ? 'Success' : 'Failed'}`);
                updateServiceDetails(data.details);
            }
        };
        
        // Service control buttons
        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');
        const btnRestart = document.getElementById('btn-restart');
        const btnEnable = document.getElementById('btn-enable');
        const btnDisable = document.getElementById('btn-disable');
        
        if (btnStart) {
            btnStart.addEventListener('click', function() {
                if (confirm(`Are you sure you want to start ${serviceName}?`)) {
                    sendServiceCommand('start');
                }
            });
        }
        
        if (btnStop) {
            btnStop.addEventListener('click', function() {
                if (confirm(`Are you sure you want to stop ${serviceName}?`)) {
                    sendServiceCommand('stop');
                }
            });
        }
        
        if (btnRestart) {
            btnRestart.addEventListener('click', function() {
                if (confirm(`Are you sure you want to restart ${serviceName}?`)) {
                    sendServiceCommand('restart');
                }
            });
        }
        
        if (btnEnable) {
            btnEnable.addEventListener('click', function() {
                if (confirm(`Enable ${serviceName} to start at boot?`)) {
                    sendServiceCommand('enable');
                }
            });
        }
        
        if (btnDisable) {
            btnDisable.addEventListener('click', function() {
                if (confirm(`Disable ${serviceName} from starting at boot?`)) {
                    sendServiceCommand('disable');
                }
            });
        }
        
        function sendServiceCommand(command) {
            ws.send(JSON.stringify({
                action: 'control_service',
                service: serviceName,
                command: command
            }));
        }
        
        function updateServiceDetails(details) {
            const serviceStatus = document.getElementById('service-status');
            const serviceEnabled = document.getElementById('service-enabled');
            const serviceOutput = document.getElementById('service-output');
            const serviceControls = document.getElementById('service-controls');
            
            // Update status
            serviceStatus.textContent = details.status;
            serviceStatus.className = `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${details.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            
            // Update enabled status
            serviceEnabled.textContent = details.enabled ? 'Yes' : 'No';
            
            // Update output
            serviceOutput.textContent = details.output;
            
            // Update control buttons
            serviceControls.innerHTML = '';
            
            if (details.active) {
                const stopBtn = document.createElement('button');
                stopBtn.id = 'btn-stop';
                stopBtn.className = 'inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
                stopBtn.textContent = 'Stop';
                stopBtn.addEventListener('click', function() {
                    if (confirm(`Are you sure you want to stop ${serviceName}?`)) {
                        sendServiceCommand('stop');
                    }
                });
                serviceControls.appendChild(stopBtn);
                
                const restartBtn = document.createElement('button');
                restartBtn.id = 'btn-restart';
                restartBtn.className = 'inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 ml-2';
                restartBtn.textContent = 'Restart';
                restartBtn.addEventListener('click', function() {
                    if (confirm(`Are you sure you want to restart ${serviceName}?`)) {
                        sendServiceCommand('restart');
                    }
                });
                serviceControls.appendChild(restartBtn);
            } else {
                const startBtn = document.createElement('button');
                startBtn.id = 'btn-start';
                startBtn.className = 'inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
                startBtn.textContent = 'Start';
                startBtn.addEventListener('click', function() {
                    if (confirm(`Are you sure you want to start ${serviceName}?`)) {
                        sendServiceCommand('start');
                    }
                });
                serviceControls.appendChild(startBtn);
            }
            
            // Add enable/disable button
            if (details.enabled) {
                const disableBtn = document.createElement('button');
                disableBtn.id = 'btn-disable';
                disableBtn.className = 'inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-2';
                disableBtn.textContent = 'Disable';
                disableBtn.addEventListener('click', function() {
                    if (confirm(`Disable ${serviceName} from starting at boot?`)) {
                        sendServiceCommand('disable');
                    }
                });
                serviceControls.appendChild(disableBtn);
            } else {
                const enableBtn = document.createElement('button');
                enableBtn.id = 'btn-enable';
                enableBtn.className = 'inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-2';
                enableBtn.textContent = 'Enable';
                enableBtn.addEventListener('click', function() {
                    if (confirm(`Enable ${serviceName} to start at boot?`)) {
                        sendServiceCommand('enable');
                    }
                });
                serviceControls.appendChild(enableBtn);
            }
        }
    });
</script>
<?= $this->endSection() ?>
