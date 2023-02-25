<?php

namespace App\Helpers;

//分页帮助类
class pageHelper
{
    public static function initPageParam($page = 0, $pageSize = 10)
    {
        if ($page <= 0) {
            $page = 1;
        }
        if ($pageSize <= 0) {
            $pageSize = 10;
        }
        if ($pageSize >= 500) {
            $pageSize = 10;
        }
        $offset = ($page - 1) * $pageSize;

        return ['page' => $page, 'pageSize' => $pageSize, 'offset' => $offset];
    }

    //生成分页数据
    public static function getPagination($page,$pageSize,$count): array
    {
        return [
            'page' => intval($page),    //当前页
            'pageSize' => intval($pageSize),    //每页条数
            'count' => $count,      //数据总数
            'pageCount' => ceil($count / $pageSize) //总页数
        ];
    }
}
