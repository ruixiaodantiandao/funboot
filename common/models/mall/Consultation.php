<?php

namespace common\models\mall;

use Yii;
use common\models\User;
use common\models\Store;

/**
 * This is the model class for table "{{%mall_consultation}}".
 *
 * @property int $id
 * @property int $store_id 商家
 * @property int $product_id 商品
 * @property string $name 名称
 * @property string|null $question 咨询
 * @property string|null $answer 回答
 * @property int $type 类型
 * @property int $sort 排序
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $created_by 创建用户
 * @property int $updated_by 更新用户
 */
class Consultation extends ConsultationBase
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mall_consultation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['store_id', 'product_id', 'type', 'sort', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['product_id', 'name'], 'required'],
            [['question', 'answer'], 'string'],
            [['name'], 'string', 'max' => 255],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        if (Yii::$app->language == Yii::$app->params['sqlCommentLanguage']) {
            return array_merge(parent::attributeLabels(), [
                'id' => Yii::t('app', 'ID'),
                'store_id' => '商家',
                'product_id' => '商品',
                'name' => '名称',
                'question' => '咨询',
                'answer' => '回答',
                'type' => '类型',
                'sort' => '排序',
                'status' => '状态',
                'created_at' => '创建时间',
                'updated_at' => '更新时间',
                'created_by' => '创建用户',
                'updated_by' => '更新用户',
            ]);
        } else {
            return array_merge(parent::attributeLabels(), [
                'id' => Yii::t('app', 'ID'),
                'store_id' => Yii::t('app', 'Store ID'),
                'product_id' => Yii::t('app', 'Product ID'),
                'name' => Yii::t('app', 'Name'),
                'question' => Yii::t('app', 'Question'),
                'answer' => Yii::t('app', 'Answer'),
                'type' => Yii::t('app', 'Type'),
                'sort' => Yii::t('app', 'Sort'),
                'status' => Yii::t('app', 'Status'),
                'created_at' => Yii::t('app', 'Created At'),
                'updated_at' => Yii::t('app', 'Updated At'),
                'created_by' => Yii::t('app', 'Created By'),
                'updated_by' => Yii::t('app', 'Updated By'),
            ]);
        }
    }
}
