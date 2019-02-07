<?php

namespace raeder\technology\craftstats\controllers;

use craft\web\Controller;
use raeder\technology\craftstats\Plugin;
use raeder\technology\craftstats\records\PageStat;
use yii\web\NotFoundHttpException;

class StatisticsController extends Controller
{

    /**
     * @param string $hash
     * @throws NotFoundHttpException
     */
    public function actionDetail(string $hash)
    {
        $stats = PageStat::find()
            ->where('urlHash = :hash', [
                'hash' => $hash,
            ])
            ->all();

        if (count($stats) < 1) {
            throw new NotFoundHttpException('Entry not found');
        }

        var_dump($stats);
    }

}
