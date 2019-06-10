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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Storage;

class Import implements WithBatchInserts, WithChunkReading, WithLimit, WithEvents, ToModel
{
    use Importable, RegistersEventListeners;

    const EVENT_BEFORE_IMPORT = 'beforeImport';
    const EVENT_AFTER_IMPORT = 'afterImport';
    const EVENT_BEFORE_SHEET = 'beforeSheet';
    const EVENT_AFTER_SHEET = 'afterSheet';

    protected $batchSize = 100;
    protected $chunkSize = 100;
    protected $each = null;
    protected $limit = 100;
    protected $filePath;
    protected static $eventListeners = [];

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
        Storage::delete($this->getFilePath($filePath));
        return $this;
    }

    /**
     * Adds a callback to an event
     *
     * @param string $event One of Import::EVENT_BEFORE_IMPORT, Import::EVENT_AFTER_IMPORT, Import::EVENT_BEFORE_SHEET and Import::EVENT_AFTER_SHEET
     * @param callable $callback
     * @return self
     */
    public function addEventListener($event, callable $callback): self
    {
        self::$eventListeners[$event] = $callback;
        return $this;
    }
    
    /**
     * Called before any import is done
     *
     * @param BeforeImport $event
     * @return void
     */
    public static function beforeImport(BeforeImport $event)
    {
        if (array_key_exists(self::EVENT_BEFORE_IMPORT, self::$eventListeners)) {
            call_user_func(self::$eventListeners[self::EVENT_BEFORE_IMPORT], $event);
        }
    }
    
    /**
     * Called after all imports are done
     *
     * @param AfterImport $event
     * @return void
     */
    public static function afterImport(AfterImport $event)
    {
        if (array_key_exists(self::EVENT_AFTER_IMPORT, self::$eventListeners)) {
            call_user_func(self::$eventListeners[self::EVENT_AFTER_IMPORT], $event);
        }
    }

    /**
     * Called before processing a sheet
     *
     * @param BeforeSheet $event
     * @return void
     */
    public static function beforeSheet(BeforeSheet $event)
    {
        if (array_key_exists(self::EVENT_BEFORE_SHEET, self::$eventListeners)) {
            call_user_func(self::$eventListeners[self::EVENT_BEFORE_SHEET], $event);
        }
    }

    /**
     * Called after processing a sheet
     *
     * @param AfterSheet $event
     * @return void
     */
    public static function afterSheet(AfterSheet $event)
    {
        if (array_key_exists(self::EVENT_AFTER_SHEET, self::$eventListeners)) {
            call_user_func(self::$eventListeners[self::EVENT_AFTER_SHEET], $event);
        }
    }
}
