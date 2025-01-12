<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\enums\YesNo;
use <?= ltrim($generator->modelClass, '\\') ?> as ActiveModel;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="card-header">
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Update') ?>, ['edit', <?= $urlParams ?>], ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Delete') ?>, ['delete', <?= $urlParams ?>], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => <?= $generator->generateString('Are you sure you want to delete this item?') ?>,
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div class="card-body">

        <?= "<?= " ?>DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-bordered table-hover box'],
            'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "                '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (isset($generator->inputType[$column->name]) && in_array($generator->inputType[$column->name], ['dropDownList', 'radioList'])) {
            echo "                ['attribute' => '" . $column->name . "', 'value' => function (\$model) { return ActiveModel::get" . Inflector::camelize($column->name) . "Labels(\$model->" . $column->name . "); }, ],\n";
        } else {
            echo "                '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
            ],
        ]) ?>

    </div>
</div>
