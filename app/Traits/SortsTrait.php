<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2019/6/14
 * Time: 11:18
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait SortsTrait
{

    /**
     * @param QueryBuilder|EloquentBuilder|Model $query
     * @param array $orders
     *
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function setOrderBy($query, $orders = [])
    {

        if (!count($orders)) {
            return $query;
        }

        if (count($orders) == count($orders, 1)) {
            $orders = [$orders];
        }

        foreach ($orders as $order) {
            if (count($order) > 1) {
                if (!in_array(strtolower($order[1]), ['asc', 'desc'])) {
                    $order[1] = 'asc';
                }
            } else {
                $order[0] = $order[0] ?? 'id';
                $order[1] = 'asc';
            }

            [$orderBy, $sortBy] = $order;

            $query->orderBy($orderBy, $sortBy);

        }

        return $query;
    }

}
