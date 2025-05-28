<?php

namespace App\Repositories\Interface;

interface QuoteRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getQuotesWithFilters
     * @param array $filters
     * @return void
     */
    public function getQuotesWithFilters(array $filters);
}
