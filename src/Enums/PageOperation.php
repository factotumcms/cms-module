<?php

namespace Wave8\Factotum\Cms\Enums;

use Wave8\Factotum\Base\Traits\ListCases;

enum PageOperation: string
{
    use ListCases;
    case SHOW_CONTENT = 'show_content';
    case CONTENT_LIST = 'content_list';
    case LINK = 'link';
    case ACTION = 'action';
}
