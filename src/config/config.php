<?php

return array(

    /**
     * Default settings for objects
     */
    'object' => array(

        /**
         * If enabled, an objects 'join_field' will be set to and must match the users 'join_field'
         * Useful when providing business to business services
         */
        'company' => array(
            'enabled' => true,
            'join_field' => 'company_id',
            'object_name' => 'Company'
        ),

    ),

);
