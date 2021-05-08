<?php

namespace common\models\mall;

use common\models\BaseModel;
use common\models\Store;
use common\models\User;
use Yii;

/**
 * This is the model base class for table "{{%mall_product_sku}}" to add your code.
 *
 * @property Product $product
 * @property Store $store
 */
class ProductSkuBase extends BaseModel
{
    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['id'], 'safe'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /** add function getXxxLabels here, detail in BaseModel **/

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'name' => Yii::t('app', 'Name'),
            'product_id' => Yii::t('app', 'Product ID'),
            'attribute_value' => Yii::t('app', 'Attribute Value'),
            'thumb' => Yii::t('app', 'Thumb'),
            'price' => Yii::t('app', 'Price'),
            'market_price' => Yii::t('app', 'Market Price'),
            'cost_price' => Yii::t('app', 'Cost Price'),
            'wholesale_price' => Yii::t('app', 'Wholesale Price'),
            'sku' => Yii::t('app', 'Sku'),
            'stock_code' => Yii::t('app', 'Stock Code'),
            'stock' => Yii::t('app', 'Stock'),
            'weight' => Yii::t('app', 'Weight'),
            'volume' => Yii::t('app', 'Volume'),
            'type' => Yii::t('app', 'Type'),
            'sort' => Yii::t('app', 'Sort'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

}