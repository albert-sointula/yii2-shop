<?php
namespace albertgeeca\shop\backend\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class VendorAsset extends AssetBundle
{
    public $sourcePath = '@vendor/albert-sointula/yii2-shop/backend/web';

    public $css = [
        'css/vendor.css',
    ];

    public $js = [
        'js/title-generation.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}