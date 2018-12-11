<?php

namespace Laraquick\Commands;

use Illuminate\Console\Command;
use DB;

class Database extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:table 
                                {table : The name of the table to act on}
                                {--where= : A comma-separated list of <column>:<sign>:<value> e.g. id:=:1,name:like:%Ezra%}
                                {--where-null= : A comma-separated list of columns that must be null}
                                {--data= : A comma-separated list of <column>:<value>}
                                {--fields= : A comma-separated list of fields to retrieve}
                                {--c|create : Indicates to perform a create operation}
                                {--r|read : Indicates to perform a read operation. This is default}
                                {--u|update : Indicates to perform an update operation}
                                {--d|delete : Indicates to perform a delete operation}
                                ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a CRUD operation on a table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tableName = $this->argument('table');
        $where = $this->option('where');
        $whereNull = $this->option('where-null');
        $data = $this->option('data');
        $fields = $this->option('fields');
        $action = 'get';
        if ($this->option('create')) {
            $action = 'insert';
        }
        if ($this->option('update')) {
            $action = 'update';
        }
        if ($this->option('delete')) {
            $action = 'delete';
        }

        $table = DB::table($tableName);

        $where = $this->prepWhere($where);
        if (count($where)) {
            $table->where($where);
        }
        
        if ($whereNull) {
            foreach (explode(',', $whereNull) as $col) {
                $table->whereNull($col);
            }
        }
        
        switch ($action) {
            case 'delete':
                if (!$this->confirm('Are you sure you want to delete all rows in table "' . $tableName . '"?')) {
                    $this->info('Canceled');
                    return;
                }
                // no break
            case 'get':
                $result = $table->$action($fields ? explode(',', $fields) : null);
                break;
            case 'insert':
            case 'update':
                $data = $this->prepData($data);
                if (!count($data)) {
                    return $this->error('Empty data found. Please use option --data');
                }
                $result = $table->$action($data);
                break;
        }

        if (is_object($result)) {
            $result = $result->map(function ($item) {
                return is_object($item) ? (array) $item : $item;
            })->toArray();
        }

        if (is_array($result)) {
            if (!count($result)) {
                return $this->info('Result: empty');
            }
            $this->info('Result:');
            $h = $action == 'get' ? $result[0] : $result;
            $headers = array_keys((array) $h);
            $this->table($headers, $result);
        } else {
            $this->info('Result: ' . $result);
        }
    }

    private function prepWhere($where)
    {
        $where = $where ? explode(',', $where) : [];
        foreach ($where as &$d) {
            $d = explode(':', $d);
        }
        return $where;
    }

    private function prepData($data)
    {
        $newData = [];
        $data = $data ? explode(',', $data) : [];
        foreach ($data as $d) {
            $parts = explode(':', $d);
            $newData[$parts[0]] = $parts[1];
        }
        return $newData;
    }
}
