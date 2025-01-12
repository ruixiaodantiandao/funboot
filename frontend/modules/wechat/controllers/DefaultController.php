<?php

namespace frontend\modules\wechat\controllers;

use common\helpers\IdHelper;
use Yii;

/**
 * Default controller for the `wechat` module
 */
class DefaultController extends BaseController
{
    public $optionalAuth = ['index'];

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render($this->action->id, []);
    }

    public function actionProfile()
    {
        var_dump(Yii::$app->wechat->getUserBySession());
        var_dump(Yii::$app->user->identity);
        return $this->render($this->action->id, []);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionPay()
    {
        $totalFee = 1;// 支付金额单位：分
        $out_trade_no = time() . substr(IdHelper::snowFlakeId(), 0, 8);

        $orderData = [
            'trade_type' => 'JSAPI', // JSAPI，NATIVE，APP...
            'body' => '支付简单说明',
            'detail' => '支付详情',
            'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['wechat/notify/wechat']), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'out_trade_no' => $out_trade_no, // 支付
            'total_fee' => $totalFee,
            'openid' => Yii::$app->params['wechat']['userInfo']['id'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
        ];

        $payment = Yii::$app->wechat->payment;
        $result = $payment->order->unify($orderData);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $config = $payment->jssdk->sdkConfig($result['prepay_id']);

            /**
             * 注意：如果需要调用扫码支付 请设置 trade_type 为 NATIVE
             *
             * 结果示例：weixin://wxpay/bizpayurl?sign=XXXXX&appid=XXXXX&mch_id=XXXXX&product_id=XXXXXX&time_stamp=XXXXXX&nonce_str=XXXXX
             */

            /**
             * $content = $payment->scheme($result['prepay_id']);
             * $qr = Yii::$app->get('qr');
             * Yii::$app->response->format = Response::FORMAT_RAW;
             * Yii::$app->response->headers->add('Content-Type', $qr->getContentType());
             *
             * return $qr->setText($content)
             * ->setSize(150)
             * ->setMargin(7)
             * ->writeString();
             */
        } else {
            vd($result);
            die();
        }

        return $this->render($this->action->id, [
            'jssdk' => $payment->jssdk, // $app通过上面的获取实例来获取
            'config' => $config
        ]);
    }
}
