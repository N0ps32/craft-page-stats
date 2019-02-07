<?php

namespace raeder\technology\craftstats\console\controllers;

use raeder\technology\craftstats\Plugin;
use yii\console\Controller;

class ProcessController extends Controller
{

    public function actionProcessEntries()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '-1');

        try {
            Plugin::getInstance()->craftProcessingService->autoCompact();
        } catch (\Exception $e) {
            $this->stderr('Processing failed: ' . $e->getMessage());
            return 1;
        }

        $this->stdout('Processing successful!');
        return 0;
    }

}
