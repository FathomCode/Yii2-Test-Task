<?php

namespace backend\controllers;

use common\controllers\RefController;
use yii\filters\AccessControl;
use common\models\Order;
use common\models\Region;
use common\models\Product;
use yii\data\ArrayDataProvider;


class ReportDevController extends RefController
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    private function getOrderProducts()
    {
        $request = $this->request;
        $period = isset($_GET['period']) ? $_GET['period'] : null;
        $region = isset($_GET['region']) ? $_GET['region'] : null;


        //to fix cache order_products names
        //$qry = Product::findActive();
        $qry = Product::find();
        $product_names = [];
        foreach($qry->each(1) as $model) {
            $product_names[$model->id] = (string)$model;
        }
        asort($product_names);



        $q = Order::findActive();
        if($region != null && $region != '') { $q->andWhere(['=', 'region_id', $region]); }

        if (
            explode(' - ', $period)[0] != null &&
            explode(' - ', $period)[1] != null
        ) {
            if(explode(' - ', $period)[0] != null) { $q->andWhere(['>=', 'date', date("Y-m-d", strtotime(explode(' - ', $period)[0]))]); }
            if(explode(' - ', $period)[1] != null) { $q->andWhere(['<=', 'date', date("Y-m-d", strtotime(explode(' - ', $period)[1]))]); }
        }

        $ordered = array();
        foreach($q->each(1) as $order) {
            foreach($order->products as $product) {
                $prod = $product->toArray([]);

                //Fix cache order_products names
                $prod['name'] = $product_names[$prod['product_id']];

                if (isset($ordered[$order->client][$prod['name']])) {
                    $ordered[$order->client][$prod['name']] += $prod['sum'];
                } else {    
                    $ordered[$order->client][$prod['name']] = $prod['sum'];
                }
            }
         
        }

        return $ordered;
    }
    private function sortingData($order_products)
    {
        //$product_names = Product::getList();//using cache list
        $qry = Product::findActive();
        $product_names = [];
        foreach($qry->each(1) as $model) {
            $product_names[$model->id] = (string)$model;
        }
        asort($product_names);


        $total = array();
        $total[' '] = 'Тотал';

        $sorted = array();
        foreach ($order_products as $client_name => $products) {
            $sorted[$client_name][' '] = $client_name;

            foreach ($product_names as $product_name) {
                $sorted[$client_name][$product_name] = 0;
                $total[$product_name] = 0;
            }
        }

        
        $products_keys = array();//fix (не задано)
        foreach ($order_products as $client_name => $products) {
            foreach ($products as $products_key => $product_price) {
                if (!in_array($products_key, $products_keys)) {
                    $products_keys[] = $products_key;
                }

                
                // Не понятно что делать с удалёнными товарами
                $sorted[$client_name][$products_key] = $product_price;
                /*if(isset($total[$products_key])) {
                    $total[$products_key] += $product_price;
                }*/

                // Не понятно что делать с удалёнными товарами
                if(isset($total[$products_key])) {
                    $total[$products_key] += $product_price;
                } else {
                    $total[$products_key] = 0;
                }


            }
        }


        //fix (не задано), добавления нуля к к незаданным названиям товаров товарам(Не понятно что делать с удалёнными товарами)
        //чтоб нее выводить все deleted, а только те которые есть в заказе 
        //products_keys
        foreach ($sorted as $client_key => $value) {
            foreach ($products_keys as $val) {
                if (!isset($value[$val])) {
                    $sorted[$client_key][$val] = 0;
                }
            }

            foreach ($value as $k => $val) {
                if ($k == ' ') { continue; }
                if (!in_array($k, $products_keys)) {
                    $sorted[$client_key][$k] = 0;
                }
            }
        }

        if (!empty($sorted)) {
            array_push($sorted, $total);
        }

        /*
        foreach ($order_products as $client_name => $products) {
            $sorted[$client_name][] = $client_name;

            foreach ($product_names as $product_name) {
                foreach ($products as $key => $product_price) {
                    $sorted[$client_name][$product_name] = 0;

                    if ( $product_name ==  $key) {
                        $sorted[$client_name][$product_name] = $product_price;
                    }
                }
            }
        }*/


        return $sorted;
    }

    public function actionIndex()
    {

        $regions = Region::getList();
        //$products = Product::getList();
        //echo "<pre>";

        $order_products = $this->getOrderProducts();
        $sortedData = $this->sortingData($order_products);


        $dataProvider = new ArrayDataProvider([
            'allModels' => $sortedData,
           'pagination' => false,
        ]);
        return $this->render('index',[
            'dataProvider' => $dataProvider,
            'regions' => $regions
        ]);
    }
}
