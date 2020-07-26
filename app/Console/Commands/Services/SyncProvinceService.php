<?php


namespace App\Console\Commands\Services;

use App\Models\Province;

class SyncProvinceService extends BaseSyncService implements SyncInterface
{
    const LOCATION_TYPE = 0;

    public function __construct($logger, $guzzleClient)
    {
        parent::__construct($logger, $guzzleClient);
        $this->buildGraphqlQuery();
    }


    public function buildGraphqlQuery()
    {
        return $this->options['json'] = [
            'query' => '
                    query GetProvinces ($type: Int)
                    {
                        area(
                            type: $type
                        )
                        {
                            id,
                            db_id,
                            name,
                        }
                    }',
            'variables' => [
                'type' => self::LOCATION_TYPE
            ]
        ];
    }

    public function syncData()
    {
        $response = $this->guzzleClient->post($this->graphqlEndpoint, $this->options);
        $contents = json_decode($response->getBody()->getContents(), true);
        $provinces = $contents['data']['area'];
        foreach ($provinces as $province) {
            Province::updateOrCreate(
                [
                    'id' => $province['db_id']
                ],
                [
                    'id' => $province['db_id'],
                    'name' => $province['name'],
                ]
            );
            $this->logger->info('Synced province ' . json_encode($province));
        }
    }
}
