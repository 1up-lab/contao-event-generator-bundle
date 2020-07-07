<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_calendar_event_generator'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_calendar',
        'onload_callback' => [
            [Oneup\Contao\EventGeneratorBundle\EventListener\CalendarEventGeneratorListener::class, 'onLoad'],
        ],
        'onsubmit_callback' => [
            [Oneup\Contao\EventGeneratorBundle\EventListener\CalendarEventGeneratorListener::class, 'onSubmit'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],

    'edit' => [
        'buttons_callback' => [
            [Oneup\Contao\EventGeneratorBundle\EventListener\CalendarEventGeneratorListener::class, 'getButtons'],
        ],
    ],

    // List
    'list' => [
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['addRegistration'],
        'default' => 'title,author;from,to,slots,weekdays;addRegistration,',
    ],

    // Subpalettes
    'subpalettes' => [
        'addRegistration' => 'registrationForm,maxParticipants,registrationDeadline',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_calendar.title',
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => [
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'author' => [
            'default' => Contao\BackendUser::getInstance()->id,
            'exclude' => true,
            'search' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => 11,
            'inputType' => 'select',
            'foreignKey' => 'tl_user.name',
            'eval' => [
                'doNotCopy' => true,
                'chosen' => true,
                'mandatory' => true,
                'includeBlankOption' => true,
                'tl_class' => 'w50',
            ],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'from' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'date',
                'mandatory' => true,
                'doNotCopy' => true,
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => 'int(10) unsigned NULL',
        ],
        'to' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'date',
                'mandatory' => true,
                'doNotCopy' => true,
                'datepicker' => true,
                'tl_class' => 'w50 wizard',
            ],
            'sql' => 'int(10) unsigned NULL',
        ],
        'slots' => [
            'default' => 'a:7:{i:0;a:2:{s:9:"startTime";s:5:"08:00";s:7:"endTime";s:5:"09:00";}i:1;a:2:{s:9:"startTime";s:5:"09:00";s:7:"endTime";s:5:"10:00";}i:2;a:2:{s:9:"startTime";s:5:"10:00";s:7:"endTime";s:5:"11:00";}i:3;a:2:{s:9:"startTime";s:5:"11:00";s:7:"endTime";s:5:"12:00";}i:4;a:2:{s:9:"startTime";s:5:"14:00";s:7:"endTime";s:5:"15:00";}i:5;a:2:{s:9:"startTime";s:5:"15:00";s:7:"endTime";s:5:"16:00";}i:6;a:2:{s:9:"startTime";s:5:"16:00";s:7:"endTime";s:5:"17:00";}}',
            'exclude' => true,
            'inputType' => 'multiColumnWizard',
            'eval' => [
                'tl_class' => 'clr',
                'mandatory' => true,
                'columnsCallback' => [Oneup\Contao\EventGeneratorBundle\EventListener\CalendarEventGeneratorListener::class, 'getSlots'],
            ],
            'sql' => 'blob NULL',
        ],
        'addRegistration' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => [
                'submitOnChange' => true,
                'tl_class' => 'm12',
            ],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'weekdays' => [
            'exclude' => true,
            'inputType' => 'checkboxWizard',
            'options_callback' => [Oneup\Contao\EventGeneratorBundle\EventListener\CalendarEventGeneratorListener::class, 'getWeekdays'],
            'reference' => &$GLOBALS['TL_LANG']['DAYS'],
            'eval' => [
                'multiple' => true,
            ],
            'sql' => 'blob NULL',
        ],
        'registrationForm' => [
            'exclude' => true,
            'inputType' => 'select',
            'options_callback' => [Oneup\Contao\EventGeneratorBundle\EventListener\CalendarEventGeneratorListener::class, 'getRegistrationForms'],
            'eval' => [
                'chosen' => true,
                'mandatory' => true,
                'includeBlankOption' => true,
                'tl_class' => 'w50 clr',
            ],
            'sql' => 'int(10) unsigned NOT NULL default 0',
        ],
        'registrationDeadline' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'clr w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'maxParticipants' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
    ],
];
