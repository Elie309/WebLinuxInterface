<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\SystemMonitor;
use App\Services\ServicesManager;

class DashboardController extends BaseController
{
    protected $navItems;
    protected $systemMonitor;
    protected $servicesManager;

    public function __construct()
    {
        $this->systemMonitor = new SystemMonitor();
        $this->servicesManager = new ServicesManager();
        
        $this->navItems = [
            [
                'title' => 'Dashboard',
                'url' => base_url('dashboard'),
                'icon' => '<svg class="mr-3 h-6 w-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>',
                'active' => false
            ],
            [
                'title' => 'Servers',
                'url' => base_url('dashboard/servers'),
                'icon' => '<svg class="mr-3 h-6 w-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>',
                'active' => false
            ],
            [
                'title' => 'Services',
                'url' => base_url('dashboard/services'),
                'icon' => '<svg class="mr-3 h-6 w-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>',
                'active' => false
            ],
            [
                'title' => 'Terminal',
                'url' => base_url('dashboard/terminal'),
                'icon' => '<svg class="mr-3 h-6 w-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>',
                'active' => false
            ],
            [
                'title' => 'Logs',
                'url' => base_url('dashboard/logs'),
                'icon' => '<svg class="mr-3 h-6 w-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>',
                'active' => false
            ],
            [
                'title' => 'Settings',
                'url' => base_url('dashboard/settings'),
                'icon' => '<svg class="mr-3 h-6 w-6 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>',
                'active' => false
            ]
        ];
    }

    public function index()
    {
        // Set active nav item
        $this->setActiveNavItem('Dashboard');
        
        // Get initial system metrics
        $metrics = $this->systemMonitor->getAllMetrics();
        
        $data = [
            'title' => 'Dashboard - Web Linux Interface',
            'navItems' => $this->navItems,
            'metrics' => $metrics,
            'websocketPort' => 8000 // Port where WebSocket server is running
        ];
        
        return view('dashboard/index', $data);
    }
    
    public function services()
    {
        // Set active nav item
        $this->setActiveNavItem('Services');
        
        // Get initial services list
        $services = $this->servicesManager->getServices();
        
        $data = [
            'title' => 'Services - Web Linux Interface',
            'navItems' => $this->navItems,
            'services' => $services,
            'websocketPort' => 8000
        ];
        
        return view('dashboard/services/index', $data);
    }
    
    public function serviceDetails($serviceName = '')
    {
        // Set active nav item
        $this->setActiveNavItem('Services');
        
        if (empty($serviceName)) {
            return redirect()->to(base_url('dashboard/services'));
        }
        
        // Get service details
        $details = $this->servicesManager->getServiceDetails($serviceName);
        
        $data = [
            'title' => $serviceName . ' - Service Details',
            'navItems' => $this->navItems,
            'serviceName' => $serviceName,
            'details' => $details,
            'websocketPort' => 8000
        ];
        
        return view('dashboard/services/details', $data);
    }
    
    /**
     * Set the active navigation item
     * 
     * @param string $activeItem The title of the active item
     */
    private function setActiveNavItem(string $activeItem)
    {
        foreach ($this->navItems as &$item) {
            $item['active'] = ($item['title'] === $activeItem);
        }
    }
}
