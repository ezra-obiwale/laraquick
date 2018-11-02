<?php

namespace Laraquick\Helpers\Excel;

use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class Import implements WithHeadingRow, WithBatchInserts, WithChunkReading, WithMappedCells, WithLimit
{
    use Importable, RegistersEventListeners;

    protected $batchSize = 100;
    protected $chunkSize = 100;
    protected $limit = 100;
    protected $mapping = [];
    protected $each;

    /**
     * Class constructor
     *
     * @param integer $batchSize @see batch()
     * @param integer $chunkSize @see chunk()
     * @param integer $limit @see limitTo()
     */
    public function __construct(int $batchSize = 100, int $chunkSize = 100, int $limit = 100)
    {
        $this->batch($batchSize)
            ->chunk($chunkSize)
            ->limitTo($limit);
    }

    /**
     * Sets the number of rows to import in a batch
     *
     * @param integer $size
     * @return self
     */
    public function batch(int $size = 100) : self
    {
        $this->batchSize = $size;
        return $this;
    }

    /**
     * Sets the number of rows to import in a chunk
     *
     * @param integer $size
     * @return self
     */
    public function chunk(int $size = 100) : self
    {
        $this->chunkSize = $size;
        return $this;
    }

    /**
     * Sets the number of rows to import
     *
     * @param integer $size
     * @return self
     */
    public function limitTo(int $size) : self
    {
        $this->limit = $size;
        return $this;
    }

    /**
     * Sets the mapping for headings
     *
     * @param array $mapping
     * @return self
     */
    public function map(array $mapping) : self
    {
        $this->mapping = $mapping;
        return $this;
    }

    /**
     * Sets the callable to be called on each row/sheet entry
     *
     * @param callable $callback
     * @return self
     */
    public function each(callable $callback) : self
    {
        $this->each = $callback;
        return $this;
    }

    /**
     * Fetches the batch size
     *
     * @return integer
     */
    public function batchSize() : int
    {
        return $this->batchSize;
    }

    /**
     * Fetches the chunk size
     *
     * @return integer
     */
    public function chunkSize() : int
    {
        return $this->chunkSize;
    }

    /**
     * Fetches the limit size
     *
     * @return integer
     */
    public function limit() : int
    {
        return $this->limit;
    }

    /**
     * Fetches the mapping for the headings
     *
     * @return array
     */
    public function mapping() : array
    {
        return $this->mapping;
    }
}