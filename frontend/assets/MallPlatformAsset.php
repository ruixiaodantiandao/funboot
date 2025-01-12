<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MallPlatformAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];

    public $js = [
    ];

    public $depends = [
        'common\widgets\adminlte\HeadJsAsset',
        'yii\web\YiiAsset',
        'common\widgets\adminlte\Bootstrap4Asset',
        'common\widgets\adminlte\Bootstrap4PluginAsset',
        'common\widgets\adminlte\BootstrapIconsAsset',
        'common\widgets\adminlte\FlagIconAsset',
        'common\widgets\funboot\assets\FunbootBootstrapFixAsset',
        'common\components\assets\CookieBarAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
