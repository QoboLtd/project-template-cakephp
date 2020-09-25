<?php

namespace App\SystemInfo;

/**
 * Server class
 *
 * This is a helper class that assists with
 * fetching a variety of server information
 * from the system.
 */
class Server
{
    /**
     * Get server information
     *
     * This method returns a number of
     * human-friendly metrics and facts about
     * the server.
     *
     * @return mixed[]
     */
    public static function getInfo(): array
    {
        $result = [
            'Hostname' => gethostname(),
            'Operating System' => static::getOperatingSystem(),
            'Machine Type' => static::getMachineType(),
            'Number of CPUs' => static::getNumberOfCpus(),
            'Total RAM' => static::getTotalRam(),
        ];

        return $result;
    }

    /**
     * Get server operating system name and release
     *
     * @return string
     */
    public static function getOperatingSystem(): string
    {
        return php_uname('s') . ' ' . php_uname('r');
    }

    /**
     * Get server machine type / hardware architecture
     *
     * @return string
     */
    public static function getMachineType(): string
    {
        return php_uname('m');
    }

    /**
     * Get server CPU count
     *
     * @return int
     */
    public static function getNumberOfCpus(): int
    {
        $result = 0;
        $cpuInfoFile = '/proc/cpuinfo';
        if (!is_file($cpuInfoFile) || !is_readable($cpuInfoFile)) {
            return $result;
        }

        $cpuInfoFile = file($cpuInfoFile);
        if (empty($cpuInfoFile)) {
            return $result;
        }

        $cpus = preg_grep("/^processor/", $cpuInfoFile);
        $result = count($cpus);

        return $result;
    }

    /**
     * Get server total RAM size
     *
     * @return string
     */
    public static function getTotalRam(): string
    {
        $result = 'N/A';
        $memoryInfoFile = '/proc/meminfo';
        if (!is_file($memoryInfoFile) || !is_readable($memoryInfoFile)) {
            return $result;
        }

        $memoryInfoFile = file($memoryInfoFile);
        if (empty($memoryInfoFile)) {
            return $result;
        }

        $totalMemory = preg_grep("/^MemTotal:/", $memoryInfoFile);
        list(, $size, $unit) = preg_split('/\s+/', $totalMemory[0], 3);
        $result = number_format((float)$size) . ' ' . $unit;

        return $result;
    }
}
