<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\enums\YesNo;
use common\models\wechat\Fan as ActiveModel;

/* @var $this yii\web\View */
/* @var $model common\models\wechat\Fan */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card fan-view">
    <div class="card-header">
        <?= Html::a(Yii::t('app', 'Update'), ['edit', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div class="card-body">

        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-bordered table-hover box'],
            'attributes' => [
                'id',
                'store_id',
                'name',
                'brief',
                'unionid',
                'openid',
                'nickname',
                'headimgurl',
                'sex',
                'groupid',
                'subscribe',
                'subscribe_time:datetime',
                'subscribe_scene',
                'tagid_list:json',
                'remark',
                'country',
                'province',
                'city',
                'language',
                'qr_scene',
                'qr_scene_str',
                'last_longitude',
                'last_latitude',
                'last_address',
                'last_updated_at:datetime',
                'type',
                'sort',
                'status',
                'created_at:datetime',
                'updated_at:datetime',
                'created_by',
                'updated_by',
            ],
        ]) ?>

    </div>
</div>
