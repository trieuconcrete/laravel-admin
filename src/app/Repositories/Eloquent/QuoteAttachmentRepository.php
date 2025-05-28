<?php

namespace App\Repositories\Eloquent;

use App\Models\QuoteAttachment;
use App\Repositories\Interface\QuoteAttachmentRepositoryInterface;

class QuoteAttachmentRepository extends BaseRepository implements QuoteAttachmentRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\QuoteAttachment $model
     */
    public function __construct(QuoteAttachment $model)
    {
        parent::__construct($model);
    }
}
