<?php

namespace Laraquick\Helpers\Excel;

use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Maatwebsite\Excel\Exceptions\NoFilePathGivenException;
use Maatwebsite\Excel\Concerns\ToModel;

class Import implements WithBatchInserts, WithChunkReading, WithLimit, ToModel
{
    use Importable, RegistersEventListeners;

    protected $batchSize = 100;
    protected $chunkSize = 100;
    protected $each = null;
    protected $limit = 100;
    protected $filePath;

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
     * Sets the model class for impport
     *
     * @param string $className
     * @return self
     */
    public function setModelClass($className) : self
    {
        $this->className = $className;
        return $this;
    }

    /**
     * Called on each row data
     *
     * @param callable $func
     * @return self
     */
    public function rowToData(callable $func) : self
    {
        $this->each = $func;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function model(array $row)
    {
        if (!$this->each) {
            return;
        }

        if (!$data = call_user_func($this->each, $row)) {
            return;
        }
        return new $this->className($data);
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
     * Sets the path to the file to work with
     *
     * @param string $filePath
     * @return self
     */
    public function setFilePath(string $filePath) : self
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @param UploadedFile|string|null $filePath
     *
     * @throws NoFilePathGivenException
     * @return UploadedFile|string
     */
    public function getFilePath($filePath = null)
    {
        $filePath = $filePath ?? $this->filePath ?? null;

        if (null === $filePath) {
            throw NoFilePathGivenException::import();
        }

        return $filePath;
    }

    /**
     * Delete the file
     *
     * @param string $filePath
     * @return self
     */
    public function delete(string $filePath = null) : self
    {
        unlink($this->getFilePath());
        return $this;
    }
}
