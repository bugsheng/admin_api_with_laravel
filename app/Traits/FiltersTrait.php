<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2019/6/14
 * Time: 11:18
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait FiltersTrait
{

    /**
     * @param QueryBuilder|EloquentBuilder|Model|\Illuminate\Database\Eloquent\Relations\* $query
     * @param array                              $filters
     *       [
     *           ['columnName', 'value'], // 默认 and 查询 =
     *           ['columnName', 'operate', 'value'], // 默认and 查询
     *           ['and|or', 'columnName', 'operate', 'value'],
     *           ['and|or', ['and|or', 'columnName', 'operate', 'value']],
     *           ['and|or', ['and|or', ['and|or', 'columnName', 'operate', 'value']]],
     *       ]
     *       columnName支持关系名称，使用'.'分割 例如 file.id 搜索主表对应的file关系的id字段
     *       2020-08-25 目前使用关系名称的查询还有点bug，需要使用全内容进行查询
     *       operate支持： = | <> | != | > | >= | < | <= | like | lLike | rLike | in | notIn | between | notBetween
     * @param string                             $boolean and|or
     *
     * @return QueryBuilder|EloquentBuilder|Model
     */
    public function setFilters($query, $filters = [], $boolean = 'and')
    {

        if (!count($filters)) {
            return $query;
        }

        if ($query instanceof Model) {
            $query = $query->newModelQuery();
        }
        $query->where(function ($item_query) use ($filters) {

            if (count($filters) == count($filters, 1)) {
                $filters = [$filters];
            }

            foreach ($filters as $filter) {
                $filter_count = count($filter);
                if ($filter_count === 2) {
                    if (!is_array($filter[1])) {
                        $temp_filters = explode(',', $filter[1]);
                        $item_query   = $this->setFilters($item_query, $temp_filters, $filter[0]);
                    } else {
                        $item_query = $this->setFilters($item_query, $filter[1], $filter[0]);
                    }
                } elseif (in_array($filter_count, [2, 3, 4])) {

                    switch ($filter_count) {
                        case 3:
                            // and | or
                            $filterType = 'and';

                            // field
                            $searchColumn         = $filter[0];
                            $searchColumnRelation = null;
                            if (stripos($filter[1], '.')) {
                                $explode              = explode('.', $filter[1]);
                                $searchColumn         = array_pop($explode);
                                $searchColumnRelation = implode('.', $explode);
                            }

                            // = | like | lLike | rLike | in | notIn | between
                            $searchType = $filter[1];

                            // 查询数据条件
                            $searchValue = $filter[2];
                            break;
                        case 4:

                            // and | or
                            $filterType = $filter[0] ? strtolower($filter[0]) : 'and';

                            // field
                            $searchColumn         = $filter[1];
                            $searchColumnRelation = null;
                            if (stripos($filter[1], '.')) {
                                $explode              = explode('.', $filter[1]);
                                $searchColumn         = array_pop($explode);
                                $searchColumnRelation = implode('.', $explode);
                            }

                            // = | like | lLike | rLike | in | notIn | between
                            $searchType = $filter[2];

                            // 查询数据条件
                            $searchValue = $filter[3];
                            break;
                        default:
                            // and | or
                            $filterType = 'and';

                            // field
                            $searchColumn         = $filter[0];
                            $searchColumnRelation = null;
                            if (stripos($filter[1], '.')) {
                                $explode              = explode('.', $filter[1]);
                                $searchColumn         = array_pop($explode);
                                $searchColumnRelation = implode('.', $explode);
                            }

                            // = | like | lLike | rLike | in | notIn | between
                            $searchType = '=';

                            // 查询数据条件
                            $searchValue = $filter[1];
                            break;
                    }

                    if (!is_null($searchValue)) {
                        if (!$searchColumnRelation) {
                            $this->modifyFilter($item_query, $searchType, $searchColumn, $searchValue, $filterType);
                        } else {
                            if ($filterType == 'and') {
                                $item_query->whereHas($searchColumnRelation,
                                    function ($temp_query) use ($searchType, $searchColumn, $searchValue) {
                                        $this->modifyFilter($temp_query, $searchType, $searchColumn, $searchValue,
                                            'and');
                                    });
                            } else {
                                $item_query->orWhereHas($searchColumnRelation,
                                    function ($temp_query) use ($searchType, $searchColumn, $searchValue) {
                                        $this->modifyFilter($temp_query, $searchType, $searchColumn, $searchValue,
                                            'and');
                                    });
                            }

                        }
                    }

                } else {
                    continue;
                }
            }
        }, null, null, $boolean);

        return $query;
    }

    /**
     * @param QueryBuilder|EloquentBuilder|Model $query
     * @param               $searchType
     * @param               $searchColumn
     * @param               $searchValue
     * @param               $filterType
     */
    private function modifyFilter(&$query, $searchType, $searchColumn, $searchValue, $filterType)
    {
        switch ($searchType) {
            case '=':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, '=', $searchValue, $filterType);
                break;
            case '<>':
            case '!=':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, '<>', $searchValue, $filterType);
                break;
            case '>':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, '>', $searchValue, $filterType);
                break;
            case '>=':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, '>=', $searchValue, $filterType);
                break;
            case '<':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, '<', $searchValue, $filterType);
                break;
            case '<=':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, '<=', $searchValue, $filterType);
                break;
            case 'like':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, 'like', '%' . $searchValue . '%', $filterType);
                break;
            case 'lLike':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, 'like', '%' . $searchValue, $filterType);
                break;
            case 'rLike':
                $searchValue = is_null($searchValue) ? '' : (string)$searchValue;
                $query->where($searchColumn, 'like', $searchValue . '%', $filterType);
                break;
            case 'in':
                $searchValue = is_array($searchValue) ? $searchValue : explode(',', $searchValue);
                $query->whereIn($searchColumn, $searchValue, $filterType);
                break;
            case 'notIn':
                $searchValue = is_array($searchValue) ? $searchValue : explode(',', $searchValue);
                $query->whereNotIn($searchColumn, $searchValue, $filterType);
                break;
            case 'between':
                $searchValue = is_array($searchValue) ? $searchValue : explode(',', $searchValue);
                $query->whereBetween($searchColumn, $searchValue, $filterType);
                break;
            case 'notBetween':
                $searchValue = is_array($searchValue) ? $searchValue : explode(',', $searchValue);
                $query->whereNotBetween($searchColumn, $searchValue, $filterType);
                break;
            case 'null':
                $query->whereNull($searchColumn, $filterType, true);
                break;
            case 'notNull':
                $query->whereNotNull($searchColumn, $filterType);
                break;
            case 'jsonContains':
                $query->whereJsonContains($searchColumn, $searchValue, $filterType);
                break;
            case 'jsonDoesntContain':
                $query->whereJsonDoesntContain($searchColumn, $searchValue, $filterType);
                break;
        }
    }
}
