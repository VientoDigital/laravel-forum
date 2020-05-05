<?php

// Customize table table names to your needs
return [
    'table_names' => [
        'settings' => 'forum_settings',
        'discussions' => 'forum_discussions',
        'discussion_users' => 'forum_discussion_user',
        'posts' => 'forum_posts',
        'tags' => 'forum_tags',
        'discussion_tags' => 'forum_discussion_tag',
    ],
    'models' => [
        'user' => 'App\Models\User',
    ],
    'views' => [
        'folder' => 'tw.', // '','tw''bs4'
    ]
];
