<?php


namespace App\Console\Commands\Services;

use App\Models\District;

class SyncDistrictService extends BaseSyncService implements SyncInterface
{
    const LOCATION_TYPE = 1;

    public function __construct($logger, $guzzleClient)
    {
        parent::__construct($logger, $guzzleClient);
        $this->buildGraphqlQuery();
    }


    public function buildGraphqlQuery()
    {
        return $this->options['json'] = [
            'query' => '
                    query GetDistricts ($type: Int)
                    {
                        area(
                            type: $type
                        )
                        {
                            id,
                            db_id,
                            name,
                            parentArea{id, db_id, name}
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
        $districts = $contents['data']['area'];
        foreach ($districts as $district) {
            District::updateOrCreate(
                [
                    'id' => $district['db_id']
                ],
                [
                    'id' => $district['db_id'],
                    'name' => $district['name'],
                    'province_id' => $district['parentArea']['db_id']
                ]
            );
            $this->logger->info('Synced district ' . json_encode($district));
        }
    }
}
