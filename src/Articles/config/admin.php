<?php
/**
 * User: Evgeniy
 */

return [
    'articles' => [
        'label' => 'Статьи',
        'entityLabel' => 'Статью',
        'entityManager' => 'ArticlesManager',
        'editTemplateUrl' => 'Articles/admin2/edit.twig',
        'list' => [
            'created' => [
                'label' => 'Создано',
                'sortable' => true,
                'listview' => "{- row.created.date | dateToISO | date: 'HH:mm dd.MM.yyyy' -}",
            ],
            'changed' => [
                'label' => 'Изменено',
                'sortable' => true,
                'listview' => "{- row.changed.date | dateToISO | date: 'HH:mm dd.MM.yyyy' -}",
            ],
            'title' => [
                'label' => 'Заголовок',
                'sortable' => true,
            ],
            'categories' => [
                'label' => 'Категории',
                'sortable' => true,
                'listview' => "{- row.categories | dictArr -}",
                'fields' => [
                    'name' => [
                        'method' => 'getName',
                    ],
                ],
            ],

        ],
        'edit' => [
            "title" => [
                'label' => 'Заголовок',
                'template' => 'formInput/text.twig',
            ],
            "categories" => [
                'label' => 'Категории',
                'template' => 'formInput/dictMultiselect.twig',
                'api' => '/api/categories/:id',
                'fields' => [
                    'name' => [
                        'method' => 'getName',
                    ],
                ],
            ],
            "description" => [
                'label' => 'Описание',
                'template' => 'formInput/textarea.twig',
            ],
            "text" => [
                'label' => 'Текст',
                'template' => 'formInput/ckeditor.twig',
            ],
            "images" => [
                'label' => 'Фотографии',
                'template' => 'Articles/admin2/images.twig',
                'api' => '/api-files/articles/',
                'fields' => [
                    'id' => [
                        'method' => 'getId',
                    ],
                    'thumb' => [
                        'method' => 'getUri',
                        'args' => ['height180'],
                    ],
                    'name' => [
                        'method' => 'getName',
                    ],
                    'description' => [
                        'method' => 'getDescription',
                    ],
                ],
            ]
        ]
    ],
    'categories' => [
        'label' => 'Категории',
        'entityLabel' => 'Категория',
        'entityManager' => 'articleCategoriesManager',
        'list' => [
            'name' => [
                'label' => 'Имя',
                'sortable' => true,
            ],
        ],
        'edit' => [
            "name" => [
                'label' => 'Имя',
                'template' => 'formInput/text.twig',
            ],
        ],
    ],
];