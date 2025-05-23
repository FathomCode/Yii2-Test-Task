<?php

use yii\helpers\Html;
use common\grid\GridView;
use common\widgets\Card;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Order */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = Yii::$app->urlManager->getLastTitle();
?>

<?php Card::begin([]); ?>

<?php echo $this->render('_search', ['regions' => $regions]);  ?>

<?= GridView::widget([
    'actions' => \common\widgets\ActionButtons::widget(['defaultShowTitle' => false, 'defaultAccess' => '$'
    ]),
   'showFooter' => true,
    'dataProvider' => $dataProvider]); ?>

<?php Card::end(); ?>