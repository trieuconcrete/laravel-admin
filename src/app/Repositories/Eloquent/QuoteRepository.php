<?php

namespace App\Repositories\Eloquent;

use App\Models\Quote;
use App\Repositories\Interface\QuoteRepositoryInterface;

class QuoteRepository extends BaseRepository implements QuoteRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\Quote $model
     */
    public function __construct(Quote $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of getQuotesWithFilters
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getQuotesWithFilters(array $filters)
    {
        $query = $this->model->with(['customer', 'attachments' => function ($query) {
            $query->whereNotNull('file_path');
        }])->latest();

        /** search status */
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        /** search keyword */
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('quote_code', 'like', '%' . $keyword . '%')
                    ->orWhereHas('customer', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('customer_code', 'like', '%' . $keyword . '%');
                    });
            });
        }

        return $query->paginate($this->getPaginationLimit());
    }
}
