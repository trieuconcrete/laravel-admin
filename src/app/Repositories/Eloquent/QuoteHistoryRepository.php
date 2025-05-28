<?php

namespace App\Repositories\Eloquent;

use App\Models\QuoteHistory;
use App\Repositories\Interface\QuoteHistoryRepositoryInterface;

class QuoteHistoryRepository extends BaseRepository implements QuoteHistoryRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\QuoteHistory $model
     */
    public function __construct(QuoteHistory $model)
    {
        parent::__construct($model);
    }
}
