<?php

namespace raeder\technology\craftstats\service;

use craft\helpers\Db;
use raeder\technology\craftstats\records\PageStat;
use yii\base\Component;

class CraftStatsService extends Component
{

    /**
     * Counts a view by url and query
     *
     * @param string $url
     * @param string $query
     * @throws \yii\db\Exception
     */
    public function countView(string $url, string $query)
    {
        $pageStat = PageStat::findOne([
            'url' => $url,
            'query' => $query,
        ]);

        if ($pageStat === null) {
            try {
                $pageStat = new PageStat();
                $pageStat->query = $query;
                $pageStat->url = $url;
                $pageStat->hitCount = 0;
                $pageStat->botHitCount = 0;
                $pageStat->insert();
            } catch (\Throwable $e) {
                // we catch here because this is likely caused by a race condition
                // when 2 requests attempt to create a new record at the same time
            }
        }

        if ($this->isRequestMadeByBot()) {
            //atomic SQL update
            $updateCommand = sprintf(
                'UPDATE %s SET botHitCount = botHitCount + 1, dateUpdated = :dateUpdated WHERE id = :id',
                PageStat::tableName()
            );
        } else {
            //atomic SQL update
            $updateCommand = sprintf(
                'UPDATE %s SET hitCount = hitCount + 1, dateUpdated = :dateUpdated WHERE id = :id',
                PageStat::tableName()
            );
        }

        PageStat::getDb()
            ->createCommand($updateCommand, [
                'id' => $pageStat->id,
                'dateUpdated' => Db::prepareDateForDb(new \DateTime()),
            ])
            ->execute();
    }

    /**
     * Returns if the website was requested by a crawler
     *
     * @return bool
     */
    private function isRequestMadeByBot(): bool
    {
        $agent = \Craft::$app->getRequest()->getUserAgent();

        if ($agent === null) {
            return false;
        }

        return preg_match('/bot|crawl|slurp|spider|mediapartners/i', $agent) === 1;
    }

}
