<?php
/**
 * POST参数转为JSON输出，方便调试查看数据
 * Created by PhpStorm.
 * User: admin <klinson@163.com>
 * Date: 2020/7/13
 * Time: 18:53
 */

function data_format($value)
{
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = data_format($item);
        }
    } else {
        if (is_numeric($value)) {
            $tmp = intval($value);
            if ($tmp == $value) $value = $tmp;
            else $value = doubleval($value);
        }
    }

    return $value;
}

$params = $_POST;
header('Content-Type: application/json; charset=utf-8');
echo json_encode(data_format($params));
