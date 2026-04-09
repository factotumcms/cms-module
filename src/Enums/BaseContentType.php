<?php

namespace Wave8\Factotum\Cms\Enums;

use Wave8\Factotum\Base\Traits\ListCases;

enum BaseContentType: string
{
    use ListCases;
    case PAGES = 'pages';
    case NEWS = 'news';
}
