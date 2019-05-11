<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'http://andreychef/css/konditerskaya_global_v2.css',
        'https://fonts.googleapis.com/css?family=Roboto:400,300,700&subset=latin,cyrillic',
        'https://fonts.googleapis.com/css?family=EB+Garamond&subset=latin,cyrillic',
        'css/style.css',
    ];
    public $js = [
        'https://3dsec.sberbank.ru/demopayment/docsite/assets/js/ipay.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
