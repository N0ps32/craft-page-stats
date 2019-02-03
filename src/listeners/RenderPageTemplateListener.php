<?php

namespace raeder\technology\craftstats\listeners;

use Craft;
use craft\web\View;
use raeder\technology\craftstats\Plugin;
use yii\base\Event;

class RenderPageTemplateListener
{

    public function addListener()
    {
        Event::on(View::class, View::EVENT_AFTER_RENDER_PAGE_TEMPLATE, function () {
            $request = Craft::$app->getRequest();
            $query = $request->getQueryStringWithoutPath();
            $url = '/' . $request->getFullPath();

            if ($query) {
                $queryStrings = explode('&', $query);
                sort($queryStrings);
                $query = implode('&', $queryStrings);
            }

            Plugin::getInstance()->craftStats->countView($url, $query);
        });
    }

}
