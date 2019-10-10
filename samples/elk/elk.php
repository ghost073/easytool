<?php
/**
 * elasticsearch 搜索demo
 *
 * @author youling073
 * @date 2019/10/10 16:29
 *
 */
include_once __DIR__.'/../../vendor/autoload.php';

use Youling073\Easytool\Elasticsearch\ElasticLib;

$config = [
    [
        'host' => '127.0.0.1',
        'port' => '9200',
        'scheme' => 'http',
        'user' => 'elastic',
        'pass' => 'di7IURw58Elo5BMJ04j5',
    ]
];


$test = new ElasticLib($config);

$method = $argv[1];

$index_param = [];
switch ($method) {
    // 创建索引 php elk.php createIndex
    case 'createIndex':
        $index_param = [
            'index.name' => 'article2',
            'settings.number_of_shards' => 2,
            'settings.number_of_replicas' => 1,
            'aliases' => 'article',
            'mappings.properties' => [
                'title' => [
                    'type' => 'text',
                    'index' => true,
                ],
                'content' => [
                    'type' => 'text',
                    'index' => true,
                ],
                'tag' => [  // 标签 多值
                    'type' => 'keyword',
                    'index' => true,
                ],
                'utime' => [
                    'type' => 'long',
                    'index' => false,
                ],
                'cateid' => [
                    'type' => 'integer',
                    'index' => true,
                ],
            ],
        ];
        break;
    // 删除索引 php elk.php delIndex article1
    case 'delIndex':
        $index_name = $argv[2];
        $index_param = [
            'source.name' => $index_name,
        ];
        break;
    // 添加文档 php elk.php createDoc 1
    case 'createDoc':
        $index_name = 'article';
        $pos_i = $argv[2];
        $index_param = [
            'index.name' => $index_name,
            'data' => [
                'id' => $pos_i,
                'title' => '天空'.$pos_i,
                'content' => '没有'.$pos_i,
                'tag' => [$pos_i, $pos_i+1, $pos_i+2],
                'utime' => time()+1,
                'cateid' => 2,
            ]
        ];
        break;
    // 添加文档 php elk.php getDocById 1
    case 'getDocById':
        $index_name = 'article';
        $doc_id = $argv[2];
        $index_param = [
            'index.name' => $index_name,
            'id' => $doc_id,
        ];
        break;
    // 搜索 php elk.php getDocArrBy
    // 搜索过滤的关键词都得加入index=true
    case 'getDocArrBy':
        $index_param = [
            'index.name' => 'article',
            'index.onlytotal' => 0,
            //'thinkfield' => ['title'],
            'thinkpage' => 1,
            'thinklimit' => 3,
            'thinkorderby' => [
                '_id' => 'desc'
            ],
            'fullsearch' => [
                // 'title' => '标题',
                //'content' => '内容7',
            ],
            'bothlike' => [
                'title' => '标题1',
                //'content' => '内容2',
            ],
            'in' => [
                'tag' => [2,4],
            ],
            'eq' => [
                'cateid' => 1,
            ],
            'neq' => [
            //    'cateid' => 2,
            ],
        ];
        break;
    // 获得表结构 php elk.php getIndexMapping article
    case 'getIndexMapping':
        $index_name = $argv[2];
        $index_param = [
            'index.name' => $index_name,
        ];
        break;
    // 更新表结构 php elk.php updateIndexMapping
    case 'updateIndexMapping':
        $index_name = 'article';
        $index_param = [
            'index.name' => $index_name,
            'properties' => [
                'cateid' => [
                    'type' => 'integer',
                    'index' => true,
                ],
            ],
        ];
        break;
    // 重建索引表结构更改 php elk.php renewIndex
    case 'renewIndex':
        $index_param = [
            'source.name' => 'article1',
            'dest.name' => 'article2',
            'alias.name' => 'article',
        ];
        break;
    default:
        break;
}
$res = call_user_func([$test, $method], $index_param);
var_dump($res);
exit;