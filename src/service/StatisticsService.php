<?php

namespace raeder\technology\craftstats\service;

use craft\helpers\Db;
use raeder\technology\craftstats\records\PageHit;
use raeder\technology\craftstats\records\PageStat;
use yii\base\Component;
use yii\db\Query;

class StatisticsService extends Component
{

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function autoCompact()
    {
        //lock down entries we'll process
        $processingKey = hash('md5', random_bytes(20));
        PageHit::updateAll(
            ['processingKey' => $processingKey],
            'processingKey IS NULL'
        );

        $pageHits = (new Query())
            ->from(PageHit::tableName())
            ->addSelect('COUNT(id) AS pageHits, SUM(isMobile) AS mobileHits, SUM(isBot) AS botHits, url, urlHash, query, SUM(pageLoadMs) AS pageLoadTotal, ua')
            ->groupBy('urlHash')
            ->where('processingKey = :key')
            ->addParams([
                'key' => $processingKey,
            ])
            ->all();

        foreach ($pageHits as $pageHit) {
            $pageStat = $this->createOrFind($pageHit['urlHash'], $pageHit['url'], $pageHit['query']);
            $pageHits = intval($pageHit['pageHits']);
            $pageLoadTotal = intval($pageHit['pageLoadTotal']);
            $pageLoadAvg = $pageLoadTotal / $pageHits;
            if ($pageStat->avgLoadTimeMs === 0) {
                $pageLoadAvgCombined = $pageLoadAvg;
            } else {
                $pageLoadAvgCombined = ($pageStat->avgLoadTimeMs + $pageLoadAvg) / 2;
            }
            $pageStat->hitCount += $pageHits;
            $pageStat->botHitCount += intval($pageHit['botHits']);
            $pageStat->mobileHitCount += intval($pageHit['mobileHits']);
            $pageStat->avgLoadTimeMs = $pageLoadAvgCombined;
            $pageStat->update();
        }

        PageHit::deleteAll([
            'processingKey' => $processingKey,
        ]);
    }

    /**
     * @param string $hash
     * @param string $url
     * @param string $query
     * @return PageStat
     * @throws \Throwable
     */
    private function createOrFind(string $hash, string $url, string $query): PageStat
    {
        $now = new \DateTime();
        $hour = intval($now->format('H'));
        $start = (new \DateTime())->setTime($hour, 0, 0);
        $start = Db::prepareDateForDb($start);
        $end = (new \DateTime())->setTime($hour, 59, 59);
        $end = Db::prepareDateForDb($end);

        $entry = PageStat::find()
            ->where('dateCreated BETWEEN :start AND :end', [
                'start' => $start,
                'end' => $end,
            ])
            ->andWhere('urlHash = :hash', ['hash' => $hash])
            ->one();

        if ($entry !== null) {
            return $entry;
        }

        $entry = new PageStat();
        $entry->urlHash = $hash;
        $entry->botHitCount = 0;
        $entry->hitCount = 0;
        $entry->mobileHitCount = 0;
        $entry->userAgents = '{}';
        $entry->url = $url;
        $entry->query = $query;
        $entry->avgLoadTimeMs = 0;
        $entry->insert();

        return $entry;
    }

}
