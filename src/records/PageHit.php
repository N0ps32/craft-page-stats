<?php

namespace raeder\technology\craftstats\records;

use craft\db\ActiveRecord;

/**
 * Page hit record.
 *
 * @property integer $id
 * @property string $url
 * @property string $query
 * @property string $ua
 * @property boolean $isMobile
 * @property boolean $isBot
 * @property string $processingKey
 * @property string $urlHash
 * @property integer $pageLoadMs
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 * @property string $uid
 */
class PageHit extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%craft_page_hit}}';
    }

}
