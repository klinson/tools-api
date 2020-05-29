<?php

return [
    'default' => env('DEFAULT_THEME', 'tools'),

    'themes' => [
        'default' => [
            'view_root_path' => 'home.default',
            'style_root_path' => 'theme/default',
            'system_name' => 'Klinson',
            'default_title' => 'Cms',
            'description' => 'klinson个人主页cms',
            'keyword' => 'klinson,cms,个人主页',
            'author' => 'klinson',
            'author_link' => 'http://klinson.com',
            'default_article_thumbnail' => 'theme/default/images/default_article_thumbnail.png',
            'default_category_thumbnail' => 'theme/default/images/default_category_thumbnail.png',
        ],

        'tools' => [
            'view_root_path' => 'home.tools',
            'style_root_path' => 'theme/tools',
            'system_name' => '工具',
            'default_title' => '工具',
            'description' => 'klinson个人主页cms,工具',
            'keyword' => 'klinson,cms,个人主页,工具',
            'author' => 'klinson',
            'author_link' => 'http://klinson.com',
            'default_article_thumbnail' => 'theme/default/images/default_article_thumbnail.png',
            'default_category_thumbnail' => 'theme/default/images/default_category_thumbnail.png',
        ],

        'page' => [
            'view_root_path' => 'home.page',
            'style_root_path' => 'theme/page',
            'system_name' => 'Klinson',
            'default_title' => 'Cms',
            'description' => 'klinson个人主页cms',
            'keyword' => 'klinson,cms,个人主页',
            'author' => 'klinson',
            'author_link' => 'http://klinson.com',
            'default_article_thumbnail' => 'theme/default/images/default_article_thumbnail.png',
            'default_category_thumbnail' => 'theme/default/images/default_category_thumbnail.png',
        ],
    ]
];
