<?php

namespace Wave8\Factotum\Cms\Enums;

use Wave8\Factotum\Base\Traits\ListCases;

enum ContentStatus: string
{
    use ListCases;
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case REVIEW = 'review';
    case PUBLISHED = 'published';

}
