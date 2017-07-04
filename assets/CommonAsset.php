<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/2
 * Time: 16:18
 */
namespace app\assets;
class CommonAsset extends  \yii\web\AssetBundle{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];
    public $js = [
        'js/comm.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}