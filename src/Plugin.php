<?php

namespace raeder\technology\craftstats;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use raeder\technology\craftstats\listeners\RenderPageTemplateListener;
use raeder\technology\craftstats\records\PageStat;
use raeder\technology\craftstats\service\CraftStatsService;
use yii\base\Event;

/**
 * Class CraftStats
 *
 * @property mixed $cpNavItem
 * @property-read CraftStatsService $craftStats
 * @package raeder\technology\craftstats
 */
class Plugin extends BasePlugin
{

    public $schemaVersion = '1.0.0';

    public $hasCpSettings = true;

    public $hasCpSection = true;

    public function init()
    {
        parent::init();

        $this->setComponents([
            'craftStats' => CraftStatsService::class
        ]);

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function (RegisterCpNavItemsEvent $event) {
                $event->navItems[] = $this->getCpNavItem();
            }
        );

        if (Craft::$app->getRequest()->getIsSiteRequest()) {
            $this->craftStats->startPerformanceTrace();
            (new RenderPageTemplateListener())->addListener();
        }

        if (!Craft::$app->getRequest()->getIsConsoleRequest()) {
            Craft::$app
                ->getView()
                ->getTwig()
                ->addGlobal('pageStatistics', PageStat::find());
        }
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Page Statistics';
        return $item;
    }

}
