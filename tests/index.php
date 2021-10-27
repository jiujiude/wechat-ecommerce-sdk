<?php
/**
 * Created by PhpStorm.
 * User: hgq <393210556@qq.com>
 * Date: 2021/10/27
 * Time: 11:41
 */

use WxPayEcommerce\Apply;
use WxPayEcommerce\WxPayv3Exception;

require_once '../vendor/autoload.php';

try {
    $obj = new Apply();
    $obj->applyment([]);
} catch (WxPayv3Exception $th) {
    var_dump($th);
}
