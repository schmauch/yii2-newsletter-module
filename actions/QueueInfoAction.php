<?php

namespace schmauch\newsletter\actions;

use yii\db\Query;
use yii\base\Action;

class QueueInfoAction extends Action
{
    public $queue;


    /**
     * Info about queue status.
     */
    public function run()
    {
        $jobs['waiting'] = $this->getWaiting()->count('*', $this->queue->db);

        $jobs['delayed'] = $this->getDelayed()->count('*', $this->queue->db);

        $jobs['reserved'] = $this->getReserved()->count('*', $this->queue->db);
        
        $jobs['done'] = $this->getDone()->count('*', $this->queue->db);
        
        return $jobs;
    }

    /**
     * @return Query
     */
    protected function getWaiting()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere(['reserved_at' => null])
            ->andWhere(['delay' => 0]);
    }

    /**
     * @return Query
     */
    protected function getDelayed()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere(['reserved_at' => null])
            ->andWhere(['>', 'delay', 0]);
    }

    /**
     * @return Query
     */
    protected function getReserved()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere('[[reserved_at]] is not null')
            ->andWhere(['done_at' => null]);
    }

    /**
     * @return Query
     */
    protected function getDone()
    {
        return (new Query())
            ->from($this->queue->tableName)
            ->andWhere(['channel' => $this->queue->channel])
            ->andWhere('[[done_at]] is not null');
    }
}
