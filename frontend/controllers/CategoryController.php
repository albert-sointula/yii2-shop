<?php
namespace albertgeeca\shop\frontend\controllers;

use albertgeeca\shop\widgets\traits\TreeWidgetTrait;
use Yii;
use yii\web\Controller;
use albertgeeca\shop\frontend\components\forms\CartForm;
use bl\cms\seo\StaticPageBehavior;
use albertgeeca\shop\frontend\components\ProductSearch;
use albertgeeca\shop\common\entities\{
    Category, Filter
};
use yii\web\NotFoundHttpException;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{
    use TreeWidgetTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'staticPage' => [
                'class' => StaticPageBehavior::className(),
                'key' => 'shop'
            ]
        ];
    }

    /**
     * @param null|integer $id
     * @return string
     * @throws NotFoundHttpException
     * Shows parent categories and products of this category if category has not children categories.
     */
    public function actionShow($id = null)
    {

        if (is_null($id)) {
            $childCategories = Category::find()
                ->where(['parent_id' => null, 'show' => true, 'additional_products' => false])
                ->orderBy(['position' => SORT_ASC])->all();
            $this->registerStaticSeoData();
        } else {
            $category = Category::find()->where(['id' => $id, 'additional_products' => false])->one();
            if (empty($category)) throw new NotFoundHttpException();

            $category->registerMetaData();

            $childCategories = $category->getChildren();
            $descendantCategories = $category->getDescendants($category);
            array_push($descendantCategories, $category);


        }
        if ($this->module->showChildCategoriesProducts || empty($childCategories)) {

            $cart = new CartForm();

            /*Filter*/
            $filter = \Yii::$app->request->post('FilterForm');
            $vendor_id = $filter['vendor_id'];
            $availability_id = $filter['availability_id'];

            $searchModel = new ProductSearch();
            $dataProvider = $searchModel->search(
                Yii::$app->request->queryParams,
                    $descendantCategories ?? null,
                    $vendor_id,
                    $availability_id
            );
        }

        return $this->render('show', [
            'category' => $category ?? null,
            'childCategories' => $childCategories,
            'filters' => $filters ?? null,
            'cart' => $cart ?? null,
            'dataProvider' => $dataProvider ?? null,
            'vendor_id' => isSet($vendor_id) ? $vendor_id : null,
            'availability_id' => isSet($availability_id) ? $availability_id : null,
        ]);

    }
}