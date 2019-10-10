<?php
namespace Youling073\Easytool\Elasticsearch;

use Elasticsearch\ClientBuilder;

class ElasticLib {

    // 客户端
    private $client = null;
    // 配置
    private $config = [
        'host' => '127.0.0.1',
        'port' => '9200',
        'scheme' => 'http',
        'user' => '',
        'pass' => '',
    ];


    /**
     * ElasticLib constructor.
     *
     * 注： 配置是一个二维数组
     * $config = [
     *     [
     *         'host' => '127.0.0.1',
     *         'port' => '9200',
     *         'scheme' => 'http',
     *         'user' => '',
     *         'pass' => '',
     *     ]
     * ];
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->client = ClientBuilder::create()->setHosts($config)->build();
    }

    /**
     * 创建索引
     *
     * @param array $param  索引参数
     *
     * @return array
     *
     *
     * https://www.elastic.co/guide/en/elasticsearch/reference/7.3/indices-create-index.html
     * https://www.elastic.co/guide/en/elasticsearch/reference/7.3/dynamic.html
     * https://www.elastic.co/guide/en/elasticsearch/reference/7.3/mapping-types.html
     */
    public function createIndex($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['index.name', 'settings.number_of_shards', 'settings.number_of_replicas', 'mappings.properties', 'aliases'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'index' => $param['index.name'],
                'body' => [
                    'settings' => [
                        'number_of_shards' => $param['settings.number_of_shards'],
                        'number_of_replicas' => $param['settings.number_of_replicas'],
                    ],
                    'mappings' => [
                        'dynamic' => false,  // 插入的数据必须显示设定才能使用
                        'properties' => $param['mappings.properties'],
                    ],
                    'aliases' => [
                        $param['aliases'] => new \StdClass(), // 别名
                    ]
                ],
            ];

            $result = $this->client->indices()->create($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 删除索引
     *
     */
    public function delIndex($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['source.name'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'index' => $param['source.name'],
            ];

            $result = $this->client->indices()->delete($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 获得映射
     *
     */
    public function getIndexMapping($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['index.name'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'index' => $param['index.name'],
            ];

            $result = $this->client->indices()->getMapping($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 修改映射
     * 已经存在的字段不能修改，只能增加新字段， 修改老字段需要重建索引
     *
     */
    public function updateIndexMapping($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['index.name', 'properties'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'index' => $param['index.name'],
                'body' => [
                    'properties' => $param['properties'],
                ],
            ];

            $result = $this->client->indices()->putMapping($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }


    /**
     * 重建索引
     *
     * 1. 需要先创建新索引，设置好字段  createIndex
     * 2. 执行此方法，导入数据  reIndex->aliasIndex
     * 3. 删除原索引别名并别名新索引 delIndexAlias
     * 4. 再次执行2 方法，导入增量数据  reIndex
     * 5. 删除原索引  delIndex
     *
     */
    public function renewIndex($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['source.name', 'dest.name', 'alias.name'];
            $this->checkParam($param, $need_keys);

            $this->reIndex($param);
            $this->aliasIndex($param);
            $this->delIndexAlias($param);
            $this->reIndex($param);
            $this->delIndex($param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    public function reIndex($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['source.name', 'dest.name'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'body' => [
                    'conflicts' => 'proceed',
                    'source' => [
                        'index' => $param['source.name'],
                    ],
                    'dest' => [ // 目标索引
                        'index' => $param['dest.name'],
                        'op_type' => 'create',
                        'version_type' => 'external',
                    ],
                ],
            ];

            $result = $this->client->reindex($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }


    /**
     * 索引别名
     *
     */
    public function aliasIndex($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['source.name', 'alias.name'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'index' => $param['source.name'],
                'name' => $param['alias.name'],
            ];

            $result = $this->client->indices()->putAlias($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 删除索引别名
     *
     */
    public function delIndexAlias($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['source.name', 'alias.name'];
            $this->checkParam($param, $need_keys);

            $index_param = [
                'index' => $param['source.name'],
                'name' => $param['alias.name'],
            ];

            $result = $this->client->indices()->deleteAlias($index_param);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 添加文档
     *
     * @param array $param  文档参数
     *
     * @return array
     *
     *
     * https://www.elastic.co/guide/en/elasticsearch/reference/7.3/docs-index_.html
     */
    public function createDoc($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['index.name', 'data'];
            $this->checkParam($param, $need_keys);

            $id = $param['data']['id'];
            unset($param['data']['id']);

            $add = [
                'index' => $param['index.name'],
                'id' => $id,
                'body' => $param['data'],
            ];

            $result = $this->client->index($add);

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 根据ID获得数据
     *
     *
     */
    public function getDocById($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['index.name', 'id'];
            $this->checkParam($param, $need_keys);

            $id = $param['id'];
            unset($param['id']);

            $wh = [
                'index' => $param['index.name'],
                'id' => $id,
            ];

            $response = $this->client->get($wh);
            // 返回的字段
            $result = $response['_source'];

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 搜索数据
     *
     * @param   index.name : 索引名
     *          index.onlytotal: 是否只返回条数 1:是
     *
     * @return array
     */
    public function getDocArrBy($param)
    {
        $err_code = 0;
        $err_msg = 'fail';
        $result = [];

        try {
            $need_keys = ['index.name', 'index.onlytotal'];
            $this->checkParam($param, $need_keys);
            // 是否只返回条数
            $onlytotal = $param['index.onlytotal'];

            $body = [];

            // 条件
            $where_sql = $this->whereSql($param);
            if (!empty($where_sql)) {
                $body['query'] = $where_sql;
            }

            if ($onlytotal == 1) {
                // 只返回一条数据 最小数据项
                $body['_source'] = ['_id'];
                $body['from'] = 0;
                $body['size'] = 1;
            } else {
                // 返回的字段
                $field_sql = $this->fieldSql($param);
                if (!empty($field_sql)) {
                    $body['_source'] = $field_sql;
                }

                // 排序
                $sort_sql = $this->sortSql($param);
                if (!empty($sort_sql)) {
                    $body['sort'] = $sort_sql;
                }
                // 分页
                $page_sql = $this->limitSql($param);
                if (!empty($page_sql)) {
                    $body['from'] = $page_sql['from'];
                    $body['size'] = $page_sql['size'];
                }
            }

            $index_param = [
                'index' => $param['index.name'],
                'body' => $body
            ];

            $response = $this->client->search($index_param);
            // 返回的字段
            //$result = $response['_source'];
            if ($onlytotal == 1) {
                $result = $response['hits']['total']['value'];
            } else {
                $hits = $response['hits']['hits'];

                foreach ($hits as $key=>$val) {
                    $result[$key] = $val['_source'];
                    $result[$key]['id'] = $val['_id'];
                }
            }

            throw new \Exception('success', 1);
        } catch (\Exception $e) {
            $err_code = $e->getCode() ?? 301;
            $err_msg = $e->getMessage() ?? 'fail';
        }

        $res = [
            'code' => $err_code,
            'msg' => $err_msg,
            'data' => $result,
        ];
        return $res;
    }

    /**
     * 组装SQL and
     *
     * @param array $where 要组装的数据
     * @return string 组装完成的SQL
     *
     *
     * $where = array(
     *     'fullsearch' => [
     *          'title' => '你好',
     *      ],
     *     'eq' => array(
     *         'status' => 1,
     *     ),
     *     'neq' => array(
     *         'status' => 1,
     *     ),
     *     'bothlike' => array(
     *         'title' => '你好',
     *     ),
     *     'in' => array(
     *         'id' => array(1,2,3),
     *     ),
     *     'lte' => array(
     *         'start_time' => '2017-10-10 10:10:10',
     *     ),
     *     'gte' => array(
     *         'start_time' => '2017-10-10 10:10:10',
     *     ),
     *     'gt' => array(
     *         'start_time' => '2017-10-10 10:10:10',
     *     ),
     *     'lt' => array(
     *         'start_time' => '2017-10-10 10:10:10',
     *     ),
     *     'beforelike' => array(
     *         'start_time' => '2017-10-10 10:10:10',
     *     ),
     *     'afterlike' => array(
     *         'start_time' => '2017-10-10 10:10:10',
     *     ),
     * );
     */
    protected function whereSql($where)
    {
        if (empty($where)) {
            return [];
        }

        // 组装成的SQL
        $sql_arr = [
            'must' => [], // 必须匹配
            'must_not' => [], // 必须不匹配
            'should' => [],  // or
            'filter' => [], // where
        ];

        foreach ($where as $key=>$val) {
            switch ($key) {
                case 'fullsearch':  // 全文搜索
                    foreach ($val as $k=>$v) {
                        $sql_arr['must'][] = [
                            'match' => [
                                $k => $v,
                            ],
                        ];
                    }
                    break;
                case 'eq':
                    foreach ($val as $k=>$v) {
                        $sql_arr['filter'][] = [
                            'term' => [
                                $k => $v,
                            ],
                        ];
                    }
                    break;
                case 'neq':
                    foreach ($val as $k=>$v) {
                        $sql_arr['must_not'][] = [
                            'term' => [
                                $k => $v,
                            ],
                        ];
                    }
                    break;
                case 'bothlike':
                    foreach ($val as $k=>$v) {
                        $sql_arr['filter'][] = [
                            'match_phrase' => [
                                $k => $v,
                            ],
                        ];
                    }
                    break;
                case 'in':
                    foreach ($val as $k=>$v) {

                        $tmp_in = [];
                        if (!is_array($v)){
                            $tmp_in = [$v];
                        } else {
                            foreach ($v as $vv) {
                                $tmp_in[] = $vv;
                            }
                        }
                        if (!empty($tmp_in)) {
                            $sql_arr['filter'][] = [
                                'terms' => [
                                    $k => $tmp_in,
                                ],
                            ];
                        }
                    }
                    break;
                case 'lte':
                    foreach ($val as $k=>$v) {
                        $sql_arr['filter'][] = [
                            'range' => [
                                $k => [
                                    'lte' => $v
                                ],
                            ],
                        ];
                    }
                    break;
                case 'gte':
                    foreach ($val as $k=>$v) {
                        $sql_arr['filter'][] = [
                            'range' => [
                                $k => [
                                    'gte' => $v
                                ],
                            ],
                        ];
                    }
                    break;
                case 'lt':
                    foreach ($val as $k=>$v) {
                        $sql_arr['filter'][] = [
                            'range' => [
                                $k => [
                                    'lt' => $v
                                ],
                            ],
                        ];
                    }
                    break;
                case 'gt':
                    foreach ($val as $k=>$v) {
                        $sql_arr['filter'][] = [
                            'range' => [
                                $k => [
                                    'gt' => $v
                                ],
                            ],
                        ];
                    }
                    break;
                default:
                    break;
            }
        }

        // 删除无数据选项
        foreach ($sql_arr as $key=>$val) {
            if (empty($val)) {
                unset($sql_arr[$key]);
            }
        }

        $res_sql = [];

        if (!empty($sql_arr)) {
            $res_sql['bool'] = $sql_arr;
        }
        return $res_sql;
    }

    /**
     * 生成order by 语句
     *
     * @param array $where 传进来的参数条件
     * @return string sql语句
     */
    protected function sortSql($where)
    {
        if (!isset($where['thinkorderby']) || empty($where['thinkorderby'])) {
            return [];
        }

        $sql_arr = [];

        foreach($where['thinkorderby'] as $key => $val) {
            $sql_arr[$key] = $val;
        }
        return $sql_arr;
    }

    /**
     * 组装LIMIT sql
     * @param int $page 当前页
     * @param int $page_size 每页条数
     * @return string limit语句
     */
    protected function limitSql($where)
    {
        if (!isset($where['thinkpage'])
            || (!isset($where['thinklimit']))) {
            return [];
        }

        $page = $where['thinkpage'];
        $page_size = $where['thinklimit'];

        // 分页
        $page = intval($page);
        $page_size = intval($page_size);

        $sql_arr = [];
        // 分页返回数据
        if (($page > 0) && ($page_size > 0)) {
            // 偏移量
            $start = ($page - 1) * $page_size;
            $start = max($start, 0);

            $sql_arr['from'] = $start;
            $sql_arr['size'] = $page_size;
        }

        return $sql_arr;
    }

    /**
     * 生成GROUP by 语句
     *
     * @param array $where 传进来的参数条件
     * @return string sql语句
     */
    protected function groupbySql($where)
    {
    }

    /**
     * 生成field 语句
     *
     * @param array $where 传进来的参数条件
     * @return string sql语句
     */
    protected function fieldSql($where)
    {
        if (!isset($where['thinkfield']) || empty($where['thinkfield'])) {
            return [];
        }

        $sql_arr = [];

        foreach($where['thinkfield'] as $val) {
            $sql_arr[] = $val;
        }
        return $sql_arr;
    }

    /**
     * 检查参数必填
     *
     * @param array $param 传递的参数
     * @param array $need_keys 必须使用的参数
     *
     * @return throw exception or true
     */
    private function checkParam($param, $need_keys = [])
    {
        $unset_keys = [];
        foreach ($param as $key=>$val) {
            if (!in_array($key, $need_keys)) {
                unset($param[$key]);
            }
        }

        foreach ($need_keys as $val) {
            if (!isset($param[$val])) {
                $unset_keys[] = $val;
            }
        }

        if (!empty($unset_keys)) {
            throw new \Exception(implode(',', $unset_keys).' key值不存在', 0);
        }

        return true;
    }
}