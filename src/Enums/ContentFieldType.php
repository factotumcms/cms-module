<?php

namespace Wave8\Factotum\Cms\Enums;

use Wave8\Factotum\Base\Traits\ListCases;

enum ContentFieldType: string
{
    use ListCases;
    case SELECT = 'select';
    case TEXT = 'text';
    case NUMBER = 'number';
    case URL = 'url';
    case TEXTAREA = 'textarea';
    case CHECKBOX = 'checkbox';
    case IMAGE_UPLOAD = 'image_upload';
    case LINKED_CONTENT = 'linked_content';
    case MULTIPLE_LINKED_CONTENT = 'multiple_linked_content';
}
