<?php

namespace App\Helpers;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class DBPreventDuplicateHelper
{
    /**
     * This method will help to avoid an error in the database: "duplicate key value violates unique constraint"
     * Put here only those queries in the callback that may have a problem with key duplication
     */
    public static function execute(Closure $callback): mixed
    {
        return (new self())->preventDuplicateKeyInPostgres($callback);
    }
    
    public function preventDuplicateKeyInPostgres(Closure $callback): mixed
    {
        try {
            return $this->transactionQueryWrapper($callback);
        } catch (QueryException $e) {
            $code = intval($e->getCode());

            if ($code === PGSQL_DUPLICATE_KEY_ERROR_CODE) {
                $table = $this->getTableNameFromSqlExpression($e->getSql());

                if ($table) {
                    $this->fixAutoIncrement($table);

                    return $this->preventDuplicateKeyInPostgres($callback);
                }
            }
        }
    }

    private function transactionQueryWrapper(Closure $callback): mixed
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        });
    }

    private function getTableNameFromSqlExpression(?string $expression): ?string
    {
        if (! $expression) {
            return null;
        }
        
        $pattern = '/^insert\sinto\s"([A-z_]+)"/';

        preg_match($pattern, $expression, $matches);

        return $matches[1] ?? null;
    }

    private function fixAutoIncrement(string $tableName): void
    {
        DB::select("SELECT setval('\"{$tableName}_id_seq\"', (SELECT MAX(id) FROM {$tableName}))");
    }
}
