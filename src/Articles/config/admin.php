<?php
/**
 * User: Evgeniy
 */

return [
    'articles' => [
        'label' => 'Статьи',
        'entityLabel' => 'Статью',
        'entityManager' => 'ArticlesManager',
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
                'listview' => "{- row.categories.join(', ') -}",
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
            ],
            "description" => [
                'label' => 'Описание',
                'template' => 'formInput/textarea.twig',
            ],
            "text" => [
                'label' => 'Текст',
                'template' => 'formInput/ckeditor.twig',
            ],
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