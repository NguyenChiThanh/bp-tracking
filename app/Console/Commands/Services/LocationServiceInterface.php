<?php


namespace App\Console\Commands\Services;


interface LocationServiceInterface
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
