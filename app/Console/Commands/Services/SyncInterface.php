<?php


namespace App\Console\Commands\Services;

interface SyncInterface
{
    /**
     * @return mixed
     */
    public function buildGraphqlQuery();

    /**
     * @return mixed
     */
    public function syncData();
}
