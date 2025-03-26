<?php

namespace App\Services;

class SystemMonitor
{
    protected $isWindows;
    
    public function __construct()
    {
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
    
    /**
     * Get all system metrics
     */
    public function getAllMetrics()
    {
        return [
            'cpu' => $this->getCpuUsage(),
            'memory' => $this->getMemoryUsage(),
            'disk' => $this->getDiskUsage()
        ];
    }
    
    /**
     * Get CPU usage percentage
     */
    public function getCpuUsage()
    {
        if ($this->isWindows) {
            return $this->getWindowsCpuUsage();
        } else {
            return $this->getLinuxCpuUsage();
        }
    }
    
    /**
     * Get memory usage information
     */
    public function getMemoryUsage()
    {
        if ($this->isWindows) {
            return $this->getWindowsMemoryUsage();
        } else {
            return $this->getLinuxMemoryUsage();
        }
    }
    
    /**
     * Get disk usage information
     */
    public function getDiskUsage()
    {
        if ($this->isWindows) {
            return $this->getWindowsDiskUsage();
        } else {
            return $this->getLinuxDiskUsage();
        }
    }
    
    /**
     * Get CPU usage on Windows
     */
    private function getWindowsCpuUsage()
    {
        $cmd = 'wmic cpu get LoadPercentage';
        exec($cmd, $output);
        
        if (isset($output[1])) {
            return (int)trim($output[1]);
        }
        
        return 0;
    }
    
    /**
     * Get CPU usage on Linux
     */
    private function getLinuxCpuUsage()
    {
        $load = sys_getloadavg();
        $cores = $this->getNumberOfCores();
        
        // Use the 1 minute load average and normalize it by CPU cores
        $cpuUsage = ($load[0] / $cores) * 100;
        return round(min($cpuUsage, 100), 2);
    }
    
    /**
     * Get number of CPU cores
     */
    private function getNumberOfCores()
    {
        if ($this->isWindows) {
            $cmd = 'wmic cpu get NumberOfCores';
            exec($cmd, $output);
            if (isset($output[1])) {
                return (int)trim($output[1]);
            }
            return 1;
        } else {
            $cmd = "grep -c ^processor /proc/cpuinfo";
            return (int)shell_exec($cmd);
        }
    }
    
    /**
     * Get memory usage on Windows
     */
    private function getWindowsMemoryUsage()
    {
        $cmd = 'wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value';
        exec($cmd, $output);
        
        $totalMemory = 0;
        $freeMemory = 0;
        
        foreach ($output as $line) {
            if (preg_match('/^TotalVisibleMemorySize=(\d+)$/', $line, $matches)) {
                $totalMemory = $matches[1] * 1024; // Convert to bytes
            }
            if (preg_match('/^FreePhysicalMemory=(\d+)$/', $line, $matches)) {
                $freeMemory = $matches[1] * 1024; // Convert to bytes
            }
        }
        
        $usedMemory = $totalMemory - $freeMemory;
        $percentUsed = ($totalMemory > 0) ? round(($usedMemory / $totalMemory) * 100, 2) : 0;
        
        return [
            'total' => $this->formatBytes($totalMemory),
            'used' => $this->formatBytes($usedMemory),
            'free' => $this->formatBytes($freeMemory),
            'percent_used' => $percentUsed
        ];
    }
    
    /**
     * Get memory usage on Linux
     */
    private function getLinuxMemoryUsage()
    {
        $cmd = 'free -b';
        exec($cmd, $output);
        
        $meminfo = preg_split('/\s+/', $output[1]);
        
        $totalMemory = $meminfo[1];
        $usedMemory = $meminfo[2];
        $freeMemory = $meminfo[3];
        
        $percentUsed = ($totalMemory > 0) ? round(($usedMemory / $totalMemory) * 100, 2) : 0;
        
        return [
            'total' => $this->formatBytes($totalMemory),
            'used' => $this->formatBytes($usedMemory),
            'free' => $this->formatBytes($freeMemory),
            'percent_used' => $percentUsed
        ];
    }
    
    /**
     * Get disk usage on Windows
     */
    private function getWindowsDiskUsage()
    {
        $cmd = 'wmic logicaldisk get caption,freespace,size /format:csv';
        exec($cmd, $output);
        
        $totalSpace = 0;
        $freeSpace = 0;
        $disks = [];
        
        // Skip the first line which is empty and the second which is the header
        for ($i = 2; $i < count($output); $i++) {
            $line = trim($output[$i]);
            if (empty($line)) continue;
            
            $values = str_getcsv($line);
            if (count($values) >= 3) {
                // Values are typically: Node, Caption, FreeSpace, Size
                $driveLetter = $values[1]; // Caption (like C:)
                $driveSpace = isset($values[3]) ? (float)$values[3] : 0; // Size
                $driveFree = isset($values[2]) ? (float)$values[2] : 0; // FreeSpace
                
                if ($driveSpace > 0) {
                    $totalSpace += $driveSpace;
                    $freeSpace += $driveFree;
                    $disks[$driveLetter] = [
                        'total' => $driveSpace,
                        'free' => $driveFree
                    ];
                }
            }
        }
        
        $usedSpace = $totalSpace - $freeSpace;
        // Ensure this is always returning a numeric value
        $percentUsed = ($totalSpace > 0) ? round(($usedSpace / $totalSpace) * 100, 2) : 0;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'percent_used' => $percentUsed, // This should be a number, not a string
            'disks' => $disks // Optional: returning individual disk information
        ];
    }
    
    /**
     * Get disk usage on Linux
     */
    private function getLinuxDiskUsage()
    {
        $cmd = 'df -B1 / | tail -n1';
        exec($cmd, $output);
        
        $parts = preg_split('/\s+/', trim($output[0]));
        
        $totalSpace = (float)$parts[1];
        $usedSpace = (float)$parts[2];
        $freeSpace = (float)$parts[3];
        
        // Ensure this is always returning a numeric value
        $percentUsed = ($totalSpace > 0) ? round(($usedSpace / $totalSpace) * 100, 2) : 0;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'percent_used' => $percentUsed // This should be a number, not a string
        ];
    }
    
    /**
     * Format bytes to human-readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
