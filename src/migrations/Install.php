<?php

namespace raeder\technology\craftstats\migrations;

use craft\db\Migration;
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
    }
}
