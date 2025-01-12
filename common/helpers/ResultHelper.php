<?php

namespace common\helpers;

use Yii;
use yii\web\Response;

/**
 * Class ResultHelper
 * @package common\helpers
 * @author funson86 <funson86@gmail.com>
 */
class ResultHelper
{
    public static function getMsg($code)
    {
        $errorCode = Yii::$app->params['errorCode'];
        return isset($errorCode[$code]) ? Yii::t('app', $errorCode[$code]) : '';
    }

    /**
     * response with error code which defined in /common/config/params.php
     *
     * @param  integer $code
     * @param  string $msg
     * @param  mixed $data
     * @param  array $map for totalCount pageCount currentPage perPage
     * @return array
     */
    public static function ret($code, $msg = null, $data = null, $map = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        !$msg && $msg = self::getMsg($code);
        !$msg && $msg = $data['message'] ?? '';

        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data ?? [],
            'map' => $map,
        ];
    }

    /**
     * response with error code which defined in /config/error.php
     * @param array $config
     * @return array|string
     */
    public static function render($config = [])
    {
        $file = $config['file'] ?? Yii::getAlias(Yii::$app->params['htmlReturnFile']);

        $config['code'] = $config['code'] ?? 'success';
        $config['title'] = $config['code'] == 'success' ? Yii::t('app', 'Operate Successfully') : Yii::t('app', 'Operation Failed');

        if (is_int($config['msg'])) {
            $errorCode = Yii::$app->params['errorCode'];
            $config['msg'] = isset($errorCode[$config['msg']]) ? Yii::t('app', $errorCode[$config['msg']]) : $config['msg'];
        }

        return CommonHelper::render($file, ['config' => $config]);
    }
}
