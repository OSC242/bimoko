<?php

use Carbon\Carbon;
use Illuminate\Support\Str;

return [
    'baseUrl' => '',
    'production' => false,
    'siteAuthor' => 'Adnan RIHAN <adnan@osc.cg>',
    'siteLocale' => 'fr_FR',
    'siteName' => 'Bimiko Podcast',
    'siteDescription' => "Le podcast congolais qui démystifie l'actualité tech",
    'contactFormEndpoint' => 'https://formspree.io/xrgygbno',

    'sounder' => [
        'accountId' => 'c6264237608448c18c5a49d5bc4057f7',
        'channelId' => 'aLn9D',
        'channelKey' => '-',
        'channelUrl' => 'https://osc242.sounder.fm/show/bimoko-osc242',
        'rss' => 'https://osc242.sounder.fm/show/aLn9D/rss.xml',
    ],

    // collections
    'collections' => [
        'posts' => [
            'author' => 'Adnan R.', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'blog/{filename}',
            'type' => 'article',
            'og' => function ($page) {
                $ogs = [
                    [
                        'article:published_time',
                        $page->getDate()->toDateString(),
                    ],
                ];

                foreach ($page->categories as $category) {
                    $ogs[] = [
                        'article:tag',
                        $category,
                    ];
                }

                return $ogs;
            },
            'inCategory' => function ($page, $category) {
                return in_array($category, $page->categories, true);
            },
        ],
        'categories' => [
            'path' => 'blog/categories/{filename}',
            'posts' => function ($page, $allPosts) {
                return $allPosts->filter(function ($post) use ($page) {
                    return $post->categories ? in_array($page->name, $post->categories, true) : false;
                });
            },
        ],
    ],

    // helpers
    'getDate' => function ($page) {
        return Carbon::createFromFormat('U', $page->date);
    },
    'getExcerpt' => function ($page, $length = 255) {
        if ($page->excerpt) {
            return $page->excerpt;
        }

        $content = preg_split('/<!-- more -->/m', $page->getContent(), 2);
        $cleaned = trim(
            strip_tags(
                preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content[0]),
                '<code>'
            )
        );

        if (count($content) > 1) {
            return $cleaned;
        }

        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated) . '…'
            : $cleaned;
    },
    'isActive' => function ($page, $path) {
        return Str::endsWith(trimPath($page->getPath()), trimPath($path));
    },
];
