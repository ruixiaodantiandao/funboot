<?php

namespace common\components\wechat;

use common\helpers\ArrayHelper;
use common\helpers\FileHelper;
use Yii;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class WechatSystem
 * @package common\components\wechat
 * @author funson86 <funson86@gmail.com>
 *
 * @property \EasyWeChat\OfficialAccount\Application $app 微信SDK实例
 * @property \EasyWeChat\Payment\Application $payment 微信支付SDK实例
 * @property \EasyWeChat\MiniProgram\Application $miniProgram 微信小程序实例
 * @property \EasyWeChat\OpenPlatform\Application $openPlatform 微信开放平台(第三方平台)实例
 * @property \EasyWeChat\Work\Application $work 企业微信实例
 * @property \EasyWeChat\OpenWork\Application $openWork 企业微信开放平台实例
 * @property \EasyWeChat\MicroMerchant\Application $microMerchant 小微商户实例
 */
class WechatSystem extends Wechat
{
    public $debug = false;

    public function init()
    {
        parent::init();

        $settings = Yii::$app->settingSystem->getSettings();

        $callbackUrl = $notifyUrl = '';
        if (!empty(Yii::$app->id)) {
            $callbackUrl = Yii::$app->request->hostInfo . Yii::$app->request->getUrl();
            $notifyUrl = Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl(['wechat/notify/index']);
        }

        Yii::$app->params['wechat']['wechatConfig'] = ArrayHelper::merge([
            // debug 模式会打印日志
            'debug' => $this->debug,

            // 基本信息
            'app_id' => $settings['wechat_appid'] ?? '',
            'secret' => $settings['wechat_appsecret'] ?? '',
            'token'     => $settings['wechat_token'] ?? '',
            'aes_key'     => $settings['wechat_encodingaeskey'] ?? '',

            /**
             * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
             * 使用自定义类名时，构造函数将会接收一个 `EasyWeChat\Kernel\Http\Response` 实例
             */
            'response_type' => 'array',

            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：
             *         debug/info/notice/warning/error/critical/alert/emergency
             * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
             * file：日志文件位置(绝对路径!!!)，要求可写权限
             */
            'log' => [
                'level' => 'debug',
                'file'  => FileHelper::createFile('@runtime/wechat/' . date('Ym'), date('d') . '.log'),
            ],
            /**
             * 接口请求相关配置，超时时间等，具体可用参数请参考：
             * http://docs.guzzlephp.org/en/stable/request-options.html
             *
             * - retries: 重试次数，默认 1，指定当 http 请求失败时重试的次数。
             * - retry_delay: 重试延迟间隔（单位：ms），默认 500
             * - log_template: 指定 HTTP 日志模板，请参考：https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
             */
            'http' => [
                'retries' => 1,
                'retry_delay' => 500,
                'timeout' => 5.0,
                // 'base_uri' => 'https://api.weixin.qq.com/',
            ],

            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => $callbackUrl,
            ]
        ], Yii::$app->params['wechat']['wechatConfig']);

        //微信支付
        Yii::$app->params['wechatPaymentConfig'] = ArrayHelper::merge([
            'app_id' => $settings['wechat_appid'] ?? '',
            'secret' => $settings['wechat_appsecret'] ?? '',
            'mch_id' => $settings['pay_wechat_mchid'] ?? '',
            'key' => $settings['pay_wechat_api_key'] ?? '',
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path' => $settings['pay_wechat_cert_path'] ?? '', // XXX: 绝对路径！！！！
            'key_path' => $settings['pay_wechat_key_path'] ?? '', // XXX: 绝对路径！！！！
            // 支付回调地址
            'notify_url' => $notifyUrl,
            'sandbox' => false, // 测试沙箱模式
        ], Yii::$app->params['wechat']['wechatPaymentConfig']);
    }

    /**
     * @param $result
     * @param bool $exception
     * @return bool|mixed|string
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function conductError($result, $exception = true)
    {
        if (!is_array($result)) {
            $result = json_decode($result, true);
        }

        if (isset($result['errcode']) && intval($result['errcode']) != 0) {
            // token过期
            if (isset($result['errcode']) && intval($result['errcode']) == 40001) {
                $this->app->access_token->getToken(true);
            }

            // 是否直接报错显示，两者都为true才报错
            $exception = Yii::$app->params['wechat']['wechat_error_exception'] && $exception ?? false;
            if ($exception) {
                throw new UnprocessableEntityHttpException($result['errmsg']);
            }

            return $result['errmsg'] ?? '';
        }

        return false;
    }

    public function getUserBySession()
    {
        return Yii::$app->session->get($this->sessionParam);
    }
}