<?php

namespace App\Event;

use MyCLabs\Enum\Enum;

/**
 * Event Name enum
 */
class EventName extends Enum
{
    // API events
    public const API_ADD_AFTER_SAVE = 'API.Add.afterSave';
    public const API_ADD_BEFORE_SAVE = 'API.Add.beforeSave';
    public const API_EDIT_AFTER_FIND = 'API.Edit.afterFind';
    public const API_EDIT_AFTER_SAVE = 'API.Edit.afterSave';
    public const API_EDIT_BEFORE_FIND = 'API.Edit.beforeFind';
    public const API_EDIT_BEFORE_SAVE = 'API.Edit.beforeSave';
    public const API_INDEX_AFTER_PAGINATE = 'API.Index.afterPaginate';
    public const API_INDEX_BEFORE_PAGINATE = 'API.Index.beforePaginate';
    public const API_INDEX_BEFORE_RENDER = 'API.Index.beforeRender';
    public const API_LOOKUP_AFTER_FIND = 'API.Lookup.afterFind';
    public const API_LOOKUP_BEFORE_FIND = 'API.Lookup.beforeFind';
    public const API_RELATED_AFTER_PAGINATE = 'API.Related.afterPaginate';
    public const API_RELATED_BEFORE_PAGINATE = 'API.Related.beforePaginate';
    public const API_RELATED_BEFORE_RENDER = 'API.Related.beforeRender';
    public const API_VIEW_AFTER_FIND = 'API.View.afterFind';
    public const API_VIEW_BEFORE_FIND = 'API.View.beforeFind';
}
