<?php

return array(


    /*
    |--------------------------------------------------------------------------
    | Parent Object
    |--------------------------------------------------------------------------
    |
    | If enabled, all objects will be and must be related to the same parent object as the current user
    | Parent model must exist along with corresponding parent_id field in all other models
    |
    | Useful for creating B2B services where data is not shared among businesses
    | Set to false to disable this feature
    |
    */

    'parent' => 'Company',

);
