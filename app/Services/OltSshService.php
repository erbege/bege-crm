<?php

namespace App\Services;

use Exception;
use phpseclib3\Net\SSH2;

class OltSshService
{
    protected $ssh;
    protected $timeout = 10; // Connect timeout

    /**
     * Connect to OLT
     */
    public function connect($ip, $username, $password, $port = 22)
    {
        try {
            $this->ssh = new SSH2($ip, $port);

            if (!$this->ssh->login($username, $password)) {
                throw new Exception("Login Failed: Authentication rejected by OLT.");
            }

            // Set timeout for read operations
            // phpseclib3 setTimeout implies read/write timeout
            $this->ssh->setTimeout($this->timeout);

            return true;
        } catch (Exception $e) {
            throw new Exception("SSH Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Execute multi-line script
     */
    public function executeScript(string $scriptContent)
    {
        if (!$this->ssh) {
            throw new Exception("Not connected to OLT.");
        }

        $commands = explode("\n", $scriptContent);
        $outputLog = [];

        foreach ($commands as $command) {
            $command = trim($command);

            // Skip comments and empty lines
            if (empty($command) || str_starts_with($command, '!')) {
                continue;
            }

            // Write command
            $this->ssh->write($command . "\n");

            // Small delay for OLT processing (crucial for older hardware)
            usleep(200000); // 200ms

            try {
                // Read response
                // In interactive shell, read() acts differently than exec()
                // phpseclib3 read() reads until timeout or pattern
                // We'll try to read whatever is available
                $response = $this->ssh->read();

                $outputLog[] = [
                    'command' => $command,
                    'response' => $response,
                    'status' => 'success'
                ];
            } catch (Exception $e) {
                // Read timed out or other error
                $outputLog[] = [
                    'command' => $command,
                    'error' => $e->getMessage(),
                    'status' => 'error' // or warning depending on strictness
                ];
            }
        }

        return $outputLog;
    }

    public function disconnect()
    {
        if ($this->ssh) {
            $this->ssh->disconnect();
            $this->ssh = null;
        }
    }
}
