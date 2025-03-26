<?php

namespace App\Services;

class ServicesManager
{
    protected $isWindows;
    
    public function __construct()
    {
        // Detect if running on Windows
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
    
    /**
     * Check if running on Windows
     * 
     * @return bool True if Windows, false if Linux/Unix
     */
    public function isWindows(): bool
    {
        return $this->isWindows;
    }
    
    /**
     * Get a list of system services
     * 
     * @return array List of services with their status
     */
    public function getServices(): array
    {
        if ($this->isWindows) {
            return $this->getWindowsServices();
        } else {
            return $this->getLinuxServices();
        }
    }
    
    /**
     * Get Linux services using systemctl
     * 
     * @return array List of Linux services
     */
    protected function getLinuxServices(): array
    {
        $services = [];
        
        // Use systemctl to list services
        exec('systemctl list-units --type=service --all --no-pager --plain', $output);
        
        // Skip the header lines
        $output = array_slice($output, 1, -7);
        
        foreach ($output as $line) {
            // Parse the line
            $parts = preg_split('/\s+/', trim($line), 5);
            if (count($parts) >= 5) {
                list($unit, $load, $active, $sub, $description) = $parts;
                
                // Only add if it's an actual service
                if (strpos($unit, '.service') !== false) {
                    $serviceName = str_replace('.service', '', $unit);
                    $services[] = [
                        'name' => $serviceName,
                        'unit' => $unit,
                        'load' => $load,
                        'active' => $active,
                        'status' => $sub,
                        'description' => $description,
                        'platform' => 'linux'
                    ];
                }
            }
        }
        
        return $services;
    }
    
    /**
     * Get Windows services using sc or wmic
     * 
     * @return array List of Windows services
     */
    protected function getWindowsServices(): array
    {
        $services = [];
        
        // Get service list using PowerShell
        $command = 'powershell -command "Get-Service | Select-Object Name, DisplayName, Status | ConvertTo-Csv -NoTypeInformation"';
        exec($command, $output);
        
        // Skip the header row
        if (count($output) > 1) {
            $header = str_getcsv(trim($output[0], '"'));
            
            for ($i = 1; $i < count($output); $i++) {
                $row = str_getcsv(trim($output[$i], '"'));
                $data = array_combine($header, $row);
                
                if (isset($data['Name']) && isset($data['Status'])) {
                    $services[] = [
                        'name' => $data['Name'],
                        'unit' => $data['Name'],
                        'load' => 'loaded', // Windows doesn't have a direct equivalent
                        'active' => $data['Status'] === 'Running' ? 'active' : 'inactive',
                        'status' => $data['Status'],
                        'description' => $data['DisplayName'] ?? $data['Name'],
                        'platform' => 'windows'
                    ];
                }
            }
        }
        
        return $services;
    }
    
    /**
     * Get details about a specific service
     * 
     * @param string $serviceName The name of the service
     * @return array Service details
     */
    public function getServiceDetails(string $serviceName): array
    {
        if ($this->isWindows) {
            return $this->getWindowsServiceDetails($serviceName);
        } else {
            return $this->getLinuxServiceDetails($serviceName);
        }
    }
    
    /**
     * Get Linux service details
     * 
     * @param string $serviceName The name of the service
     * @return array Service details
     */
    protected function getLinuxServiceDetails(string $serviceName): array
    {
        $details = [
            'name' => $serviceName,
            'status' => 'unknown',
            'active' => false,
            'enabled' => false,
            'output' => '',
            'platform' => 'linux'
        ];
        
        // Get service status
        exec("systemctl status {$serviceName}.service 2>&1", $outputStatus, $returnVar);
        $details['output'] = implode("\n", $outputStatus);
        
        // Check if active
        exec("systemctl is-active {$serviceName}.service 2>&1", $outputActive);
        $details['active'] = (trim($outputActive[0] ?? '') === 'active');
        $details['status'] = trim($outputActive[0] ?? '');
        
        // Check if enabled
        exec("systemctl is-enabled {$serviceName}.service 2>&1", $outputEnabled);
        $details['enabled'] = (trim($outputEnabled[0] ?? '') === 'enabled');
        
        return $details;
    }
    
    /**
     * Get Windows service details
     * 
     * @param string $serviceName The name of the service
     * @return array Service details
     */
    protected function getWindowsServiceDetails(string $serviceName): array
    {
        $details = [
            'name' => $serviceName,
            'status' => 'unknown',
            'active' => false,
            'enabled' => false,
            'output' => '',
            'platform' => 'windows'
        ];
        
        // Get service details using PowerShell
        $command = 'powershell -command "Get-Service -Name \'' . $serviceName . '\' | Select-Object Name, DisplayName, Status, StartType | Format-List"';
        exec($command, $outputStatus, $returnVar);
        
        if ($returnVar === 0) {
            $details['output'] = implode("\n", $outputStatus);
            
            // Parse status from output
            foreach ($outputStatus as $line) {
                if (strpos($line, 'Status') !== false) {
                    $statusParts = explode(':', $line, 2);
                    if (count($statusParts) > 1) {
                        $status = trim($statusParts[1]);
                        $details['status'] = $status;
                        $details['active'] = ($status === 'Running');
                    }
                }
                
                if (strpos($line, 'StartType') !== false) {
                    $startTypeParts = explode(':', $line, 2);
                    if (count($startTypeParts) > 1) {
                        $startType = trim($startTypeParts[1]);
                        $details['enabled'] = in_array($startType, ['Automatic', 'AutomaticDelayedStart']);
                    }
                }
            }
            
            // Get more detailed info for the output
            $command = 'powershell -command "Get-WmiObject -Class Win32_Service -Filter \"Name=\'' . $serviceName . '\'\" | Format-List *"';
            exec($command, $detailedOutput);
            $details['output'] .= "\n\n" . implode("\n", $detailedOutput);
        } else {
            $details['output'] = "Error: Service not found or access denied.";
        }
        
        return $details;
    }
    
    /**
     * Control a system service (start, stop, restart, etc.)
     * 
     * @param string $serviceName The name of the service
     * @param string $action The action to perform (start, stop, restart, enable, disable)
     * @return bool Success status
     */
    public function controlService(string $serviceName, string $action): bool
    {
        if ($this->isWindows) {
            return $this->controlWindowsService($serviceName, $action);
        } else {
            return $this->controlLinuxService($serviceName, $action);
        }
    }
    
    /**
     * Control a Linux service
     * 
     * @param string $serviceName The name of the service
     * @param string $action The action to perform
     * @return bool Success status
     */
    protected function controlLinuxService(string $serviceName, string $action): bool
    {
        if (!in_array($action, ['start', 'stop', 'restart', 'enable', 'disable'])) {
            return false;
        }
        
        exec("systemctl {$action} {$serviceName}.service 2>&1", $output, $returnVar);
        
        return ($returnVar === 0);
    }
    
    /**
     * Control a Windows service
     * 
     * @param string $serviceName The name of the service
     * @param string $action The action to perform
     * @return bool Success status
     */
    protected function controlWindowsService(string $serviceName, string $action): bool
    {
        $command = '';
        
        switch ($action) {
            case 'start':
                $command = 'Start-Service -Name "' . $serviceName . '"';
                break;
                
            case 'stop':
                $command = 'Stop-Service -Name "' . $serviceName . '"';
                break;
                
            case 'restart':
                $command = 'Restart-Service -Name "' . $serviceName . '" -Force';
                break;
                
            case 'enable':
                $command = 'Set-Service -Name "' . $serviceName . '" -StartupType Automatic';
                break;
                
            case 'disable':
                $command = 'Set-Service -Name "' . $serviceName . '" -StartupType Disabled';
                break;
                
            default:
                return false;
        }
        
        exec('powershell -command "' . $command . '"', $output, $returnVar);
        
        return ($returnVar === 0);
    }
}
