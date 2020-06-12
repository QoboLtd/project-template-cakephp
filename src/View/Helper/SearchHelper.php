<?php

namespace App\View\Helper;

use App\Utility\Search;
use Cake\View\Helper;
use Search\Model\Entity\SavedSearch;

final class SearchHelper extends Helper
{
    /**
     * Fields getter.
     *
     * @param string $tableName Table name
     * @return mixed[]
     */
    public function getFields(string $tableName): array
    {
        return Search::getFields($tableName);
    }

    /**
     * Search filters getter.
     *
     * @param string $tableName Table name
     * @return mixed[]
     */
    public function getFilters(string $tableName): array
    {
        return Search::getFilters($tableName);
    }

    /**
     * Method that retrieves target table search display fields.
     *
     * @param string $tableName Table name
     * @return string[]
     */
    public function getDisplayFields(string $tableName): array
    {
        return Search::getDisplayFields($tableName);
    }

    /**
     * Method that returns the label for the provided table name.
     *
     * @param string $tableName Table name
     * @return string
     */
    public function getTableLabel(string $tableName): string
    {
        return Search::getTableLabel($tableName);
    }

    /**
     * Chart options getter.
     *
     * @param \Search\Model\Entity\SavedSearch $entity Saved search entity
     * @return mixed[]
     */
    public function getChartOptions(SavedSearch $entity): array
    {
        return Search::getChartOptions($entity);
    }
}
