<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 22:00
 */

/**
 * @param $class
 * @param $times
 * @param array $attributes
 * @return mixed
 */
function create($class, $attributes = [], $times = null)
{

    return factory($class, $times)->create($attributes);
}

/**
 * @param $class
 * @param $times
 * @param array $attributes
 * @return mixed
 */
function make($class, $attributes = [], $times = null)
{

    return factory($class, $times)->make($attributes);
}