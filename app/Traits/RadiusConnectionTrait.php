<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

trait RadiusConnectionTrait
{
    /**
     * Check if the Radius database connection is available.
     *
     * @return bool
     */
    protected function isRadiusAvailable(): bool
    {
        try {
            DB::connection('radius')->select('SELECT 1');
            return true;
        } catch (QueryException $e) {
            Log::warning('Radius database is unreachable: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::warning('Radius connection check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute a callback with Radius connection error handling.
     * Returns the fallback value if the connection fails.
     *
     * @param callable $callback The function that queries the Radius database
     * @param mixed $fallback The value to return if the connection fails
     * @return mixed
     */
    protected function withRadiusConnection(callable $callback, mixed $fallback = null): mixed
    {
        try {
            return $callback();
        } catch (QueryException $e) {
            if ($this->isConnectionError($e)) {
                Log::warning('Radius database is unreachable during operation: ' . $e->getMessage());
                return $fallback;
            }
            // Re-throw if it's a query error, not a connection error
            throw $e;
        }
    }

    /**
     * Determine if a QueryException is a connection-level error.
     *
     * @param QueryException $e
     * @return bool
     */
    protected function isConnectionError(QueryException $e): bool
    {
        $connectionErrors = [
            2002, // Connection refused / Network unreachable
            2003, // Can't connect to MySQL server
            2005, // Unknown MySQL server host
            2006, // MySQL server has gone away
            2013, // Lost connection to MySQL server
            1045, // Access denied
            1049, // Unknown database
        ];

        $previous = $e->getPrevious();
        if ($previous instanceof \PDOException) {
            $code = (int) $previous->getCode();
            // PDO uses SQLSTATE codes like 'HY000', but errorInfo[1] has the MySQL code
            if (isset($previous->errorInfo[1])) {
                return in_array((int) $previous->errorInfo[1], $connectionErrors);
            }
            // Fallback: check the message for common patterns
            return str_contains($e->getMessage(), 'Network is unreachable')
                || str_contains($e->getMessage(), 'Connection refused')
                || str_contains($e->getMessage(), 'No connection could be made')
                || str_contains($e->getMessage(), 'server has gone away')
                || str_contains($e->getMessage(), 'Lost connection');
        }

        return false;
    }
}
