<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\components\enums\YesNo;
use common\models\base\SettingType as ActiveModel;
use yii\helpers\Inflector;
use jianyan\treegrid\TreeGrid;
use common\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel common\models\ModelSearch */

$this->title = Yii::t('app', 'Setting Types');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><?= !is_null($this->title) ? Html::encode($this->title) : Inflector::camelize($this->context->id);?> <?= Html::aHelp(Yii::$app->params['helpUrl'][Yii::$app->language]['Setting Types'] ?? null) ?></h2>
                <div class="card-tools">
                    <?= Html::createModal() ?>
                    <?= Html::export() ?>
                    <?= Html::import() ?>
                </div>
            </div>
            <div class="card-body">
                <?= \jianyan\treegrid\TreeGrid::widget([
                    'dataProvider' => $dataProvider,
                    'keyColumnName' => 'id',
                    'parentColumnName' => 'parent_id',
                    'parentRootValue' => '0', //first parentId value
                    'pluginOptions' => [
                        'initialState' => 'collapsed',
                    ],
                    'options' => ['class' => 'table table-hover tab-treegrid'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],

                        // 'id',
                        // ['attribute' => 'store_id', 'visible' => $this->context->isAdmin(), 'value' => function ($model) { return $model->store->name; }, 'filter' => Html::activeDropDownList($searchModel, 'store_id', ArrayHelper::map($this->context->getStores(), 'id', 'name'), ['class' => 'form-control', 'prompt' => Yii::t('app', 'Please Filter')]),],
                        // ['attribute' => 'parent_id', 'value' => function ($model) { return ActiveModel::getParentIdLabels($model->parent_id); }, 'filter' => Html::activeDropDownList($searchModel, 'parent_id', ActiveModel::getParentIdLabels(), ['class' => 'form-control', 'prompt' => Yii::t('app', 'Please Filter')]),],
                        // 'app_id',
                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column) {
                                $str = Html::tag('span', $model->name, ['class' => 'm-l-sm']);
                                $str .= Html::a(' <i class="fa fa-plus"></i>', ['edit-ajax', 'parent_id' => $model['id']], [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]);
                                return Html::tag('span', $str);
                            }
                        ],

                        //['attribute' => 'name', 'format' => 'raw', 'value' => function ($model) { return Html::field('name', $model->name); }],
                        // 'code',
                        // 'brief',
                        // ['attribute' => 'type', 'value' => function ($model) { return ActiveModel::getTypeLabels($model->type); }, 'filter' => Html::activeDropDownList($searchModel, 'type', ActiveModel::getTypeLabels(), ['class' => 'form-control', 'prompt' => Yii::t('app', 'Please Filter')]),],
                        // 'value_range',
                        // 'value_default',
                        ['attribute' => 'sort', 'format' => 'raw', 'value' => function ($model) { return Html::sort($model->sort); }],
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
