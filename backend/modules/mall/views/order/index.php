<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\components\enums\YesNo;
use common\models\mall\Order as ActiveModel;
use yii\helpers\Inflector;
use common\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel common\models\ModelSearch */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><?= !is_null($this->title) ? Html::encode($this->title) : Inflector::camelize($this->context->id);?> <?= Html::aHelp(Yii::$app->params['helpUrl'][Yii::$app->language]['Orders'] ?? null) ?></h2>
                <div class="card-tools">
                    <?= Html::createModal() ?>
                    <?= Html::export() ?>
                    <?= Html::import() ?>
                </div>
            </div>
            <div class="card-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],

                        // 'id',
                        ['attribute' => 'store_id', 'visible' => $this->context->isAdmin(), 'value' => function ($model) { return $model->store->name; }, 'filter' => Html::activeDropDownList($searchModel, 'store_id', ArrayHelper::map($this->context->getStores(), 'id', 'name'), ['class' => 'form-control', 'prompt' => Yii::t('app', 'Please Filter')]),],
                        // 'user_id',
                        ['attribute' => 'name', 'format' => 'raw', 'value' => function ($model) { return Html::field('name', $model->name); }, 'filter' => true,],
                        'sn',
                        // 'consignee',
                        // 'country_id',
                        // 'province_id',
                        // 'city_id',
                        // 'district_id',
                        // 'state',
                        'address',
                        // 'address1',
                        // 'address2',
                        // 'zipcode',
                        'mobile',
                        // 'email:email',
                        // 'distance',
                        // 'remark',
                        // 'payment_method',
                        // 'payment_fee',
                        // 'payment_status',
                        // 'paid_at:datetime',
                        // 'shipment_id',
                        // 'shipment_name',
                        // 'shipment_fee',
                        // 'shipment_status',
                        // 'shipped_at:datetime',
                        // 'product_amount',
                        // 'amount',
                        // 'extra_fee',
                        // 'discount',
                        // 'tax',
                        // 'invoice',
                        // 'type',
                        // ['attribute' => 'sort', 'format' => 'raw', 'value' => function ($model) { return Html::sort($model->sort); }, 'filter' => false,],
                        // ['attribute' => 'status', 'format' => 'raw', 'value' => function ($model) { return Html::status($model->status); }, 'filter' => Html::activeDropDownList($searchModel, 'status', ActiveModel::getStatusLabels(), ['class' => 'form-control', 'prompt' => Yii::t('app', 'Please Filter')]),],
                        // 'created_at:datetime',
                        // 'updated_at:datetime',
                        // 'created_by',
                        // 'updated_by',

                        Html::actionsModal(),
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>
