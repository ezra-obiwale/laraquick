<?php

namespace Laraquick\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * The match string for the full text operation
     *
     * @var string
     */
    private $fullTextMatchString;
    /**
     * The parameters for ordering by relevance
     *
     * @var array
     */
    private $orderParams = [];

    /**
     * Remove symbols and mark important words
     *
     * @param string $text
     * @return string
     */
    protected function treat($text): string
    {
        // remove all symbols
        if ($newText = preg_replace("/[^a-zA-Z0-9]/", '', $text)) {
            $text = $newText;
        }

        $words = explode(' ', $text);
        foreach ($words as &$word) {
            // apply required operator (+) to big words
            if (strlen($word) >= 3) {
                $word = '+' . $word . '*';
            }
        }

        return implode(' ', $words);
    }

    /**
     * Sets the name of the relevance score field
     *
     * @return string
     */
    protected function relevanceScoreName(): string
    {
        return 'relevance_score';
    }

    /**
     * Fetch the searchable columns
     *
     * @return array
     */
    protected function searchableColumns(): array
    {
        return $this->searchable ?? [];
    }

    /**
     * Perform a full text search on the given text
     *
     * @param Builder $query
     * @param string $text
     * @return Builder
     */
    public function scopeSearch(Builder $query, $text): Builder
    {
        if ($text) {
            $columns = '`' . $this->getTable() . '`.`'
                . implode("`, `{$this->getTable()}`.`", $this->searchableColumns())
                . '`';

            $this->fullTextMatchString = "MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)";
            $query->whereRaw($this->fullTextMatchString, $this->treat($text));

            if (count($this->orderParams)) {
                $this->scopeOrderByRelevance($query, ...$this->orderParams);
            }
        }

        return $query;
    }

    /**
     * Perform a full text search according to relevance scores
     *
     * @param Builder $query
     * @param string $direction
     * @param string $relevanceScoreName
     * @return Builder
     */
    public function scopeOrderByRelevance(Builder $query, $direction = 'desc', $relevanceScoreName = null): Builder
    {
        if (!$this->fullTextMatchString) {
            $this->orderParams = [$direction, $relevanceScoreName];
        } else {
            $relevanceScoreName = $relevanceScoreName ?: $this->relevanceScoreName();
            $query->selectRaw($this->fullTextMatchString . " AS {$relevanceScoreName}")
                ->orderBy($relevanceScoreName, $direction);
        }

        return $query;
    }
}
