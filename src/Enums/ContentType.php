<?php

namespace Wave8\Factotum\Cms\Enums;

use Wave8\Factotum\Base\Traits\ListCases;

enum ContentType: string
{
    use ListCases;
    case PAGE = 'page';
    case NEWS = 'news';
}
