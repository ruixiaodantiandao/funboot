<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\enums\YesNo;
use common\models\base\Setting as ActiveModel;

/* @var $this yii\web\View */
/* @var $model common\models\base\Setting */
/* @var $form yii\widgets\ActiveForm */

$this->title = ($model->id ? Yii::t('app', 'Edit ') : Yii::t('app', 'Create ')) . Yii::t('app', 'Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
        <div class="card">
            <div class="card-header"><h2 class="card-title"><?= $this->title ?></h2></div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-sm-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                        'options' => ['class' => 'form-group row'],
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'store_id')->textInput() ?>
                    <?= $form->field($model, 'app_id')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'type')->dropDownList(ActiveModel::getTypeLabels()) ?>
                    <?= $form->field($model, 'sort')->textInput() ?>
                    <?= $form->field($model, 'status')->radioList(ActiveModel::getStatusLabels()) ?>
                    <?= $form->field($model, 'created_at')->textInput() ?>
                    <?= $form->field($model, 'updated_at')->textInput() ?>
                    <?= $form->field($model, 'created_by')->textInput() ?>
                    <?= $form->field($model, 'updated_by')->textInput() ?>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
                        <span class="btn btn-white" onclick="history.go(-1)"><?= Yii::t('app', 'Back') ?></span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
