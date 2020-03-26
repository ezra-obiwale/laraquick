<?php

namespace Laraquick\Helpers;

use Illuminate\Support\Facades\DB as iDB;
use Exception;

class DB
{
    protected $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public static function table($tableName)
    {
        return new static($tableName);
    }

    public function fullText($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $key = $this->getFullTextKeyName($columns);
        $columns_string = implode(',', $columns);

        iDB::statement("ALTER TABLE {$this->tableName} ADD FULLTEXT {$key} ({$columns_string})");

        return $this;
    }

    protected function getFullTextKeyName(array $columns)
    {
        return implode('_', $columns) . '_fulltext';
    }

    public function dropFullText($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $key = $this->getFullTextKeyName($columns);
        iDB::statement("ALTER TABLE {$this->tableName} DROP INDEX {$key}");

        return $this;
    }

    public static function transaction(callable $func, callable $catch = null)
    {
        try {
            iDB::beginTransaction();
            $result = call_user_func($func);
            iDB::commit();

            return $result;
        } catch (Exception $ex) {
            iDB::rollback();

            if ($catch) {
                return call_user_func($catch, $ex);
            } else {
                throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
    }
}
