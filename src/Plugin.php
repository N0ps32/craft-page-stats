<?php

namespace raeder\technology\craftstats;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use raeder\technology\craftstats\listeners\RenderPageTemplateListener;
use raeder\technology\craftstats\records\PageStat;
use raeder\technology\craftstats\service\CraftStatsService;
use raeder\technology\craftstats\service\StatisticsService;
use yii\base\Event;

/**
 * Class CraftStats
 *
 * @property mixed $cpNavItem
 * @property-read CraftStatsService $craftStats
 * @property-read StatisticsService $craftProcessingService
 * @package raeder\technology\craftstats
 */
class Plugin extends BasePlugin
{

    public $schemaVersion = '1.0.0';

    public $hasCpSettings = true;

    public $hasCpSection = true;

    public function init()
    {
        $this->setComponents([
            'craftStats' => CraftStatsService::class,
            'craftProcessingService' => StatisticsService::class,
        ]);

        if (Craft::$app->getRequest()->getIsSiteRequest()) {
            $this->craftStats->startPerformanceTrace();
            (new RenderPageTemplateListener())->addListener();
        }

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function (RegisterCpNavItemsEvent $event) {
                $event->navItems[] = $this->getCpNavItem();
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $e) {
                /** @var CraftVariable $variable */
                $variable = $e->sender;

                // Attach a service:
                $variable->set('pageStat', PageStat::find());
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-page-stats/detail/<hash:[a-z0-9]{64}>'] = 'craft-page-stats/statistics/detail';
            }
        );

        parent::init();
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Page Statistics';
        return $item;
    }

}
