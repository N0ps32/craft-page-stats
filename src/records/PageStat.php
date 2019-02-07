<?php

namespace raeder\technology\craftstats\records;

use craft\db\ActiveRecord;

/**
 * Page stat record.
 *
 * @property integer $id
 * @property string $url
 * @property string $query
 * @property int $hitCount
 * @property int $botHitCount
 * @property int $mobileHitCount
 * @property string $userAgents
 * @property string $urlHash
 * @property int $avgLoadTimeMs
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 * @property string $uid
 */
class PageStat extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%craft_page_stats}}';
    }

}
