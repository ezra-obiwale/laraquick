<?php

namespace Laraquick\Helpers;

use Illuminate\Notifications\Messages\MailMessage as iMailMessage;

class MailMessage extends iMailMessage
{
    protected $table = [];

    public function setTableTitle(string $title) : self
    {
        $this->table['title'] = $title;

        return $this;
    }

    public function setTableHeaders(array $headers) : self
    {
        $this->table['headers'] = $headers;

        return $this;
    }

    public function addTableRow(array $rowData, int $rowIndex = null) : self
    {
        if ($rowIndex) {
            $this->table['rows'][$rowIndex] = $rowData;
        } else {
            $this->table['rows'][] = $rowData;
        }

        return $this;
    }

    public function setTableFooters(array $footers) : self
    {
        $this->table['footers'] = $footers;

        return $this;
    }

    public function table(array $table) : self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with($line)
    {
        if ($line instanceof Action) {
            $this->action($line->text, $line->url);
        } elseif (!$this->actionText && !count($this->table)) {
            $this->introLines[] = $this->formatLine($line);
        } else {
            $this->outroLines[] = $this->formatLine($line);
        }

        return $this;
    }

    /**
     * Creates the MailMessage from an array of data
     *
     * @param array $data
     * @return self
     */
    public function hydrate(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        return $this;
    }

    public function toArray() : array
    {
        $array = parent::toArray();
        $array['table'] = $this->table;
        return $array;
    }
}
