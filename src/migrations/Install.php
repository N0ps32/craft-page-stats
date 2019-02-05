<?php

namespace raeder\technology\craftstats\migrations;

use craft\db\Migration;
use raeder\technology\craftstats\records\PageHit;
use raeder\technology\craftstats\records\PageStat;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(PageStat::tableName(), [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'query' => $this->string(),
            'hitCount' => $this->integer(),
            'botHitCount' => $this->integer(),
            'avgLoadTimeMs' => $this->integer(),
            'userAgents' => $this->string(),
            'dateCreated' => $this->dateTime(),
            'dateUpdated' => $this->dateTime(),
            'uid' => $this->string(),
        ]);
        $this->createTable(PageHit::tableName(), [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'query' => $this->string(),
            'ua' => $this->string(),
            'isMobile' => $this->boolean(),
            'isBot' => $this->boolean(),
            'processing' => $this->boolean()->defaultValue(0),
            'pageLoadMs' => $this->integer(),
            'dateCreated' => $this->dateTime(),
            'dateUpdated' => $this->dateTime(),
            'uid' => $this->string(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(PageStat::tableName());
        $this->dropTable(PageHit::tableName());
    }
}
