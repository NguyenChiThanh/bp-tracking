<?php


namespace App\Console\Commands\Services;

use App\Models\Province;
use GuzzleHttp\Client;
use Illuminate\Log\Logger;

class SyncProvinceService extends BaseSyncService implements SyncInterface
{
    const LOCATION_TYPE = 0;

    public function __construct(Logger $logger, Client $guzzleClient)
    {
        parent::__construct($logger, $guzzleClient);
        $this->buildGraphqlQuery();
    }

    /**
     * @return array|mixed
     */
    public function buildGraphqlQuery()
    {
        return $this->options['json'] = [
            'query' => '
                    query GetProvinces ($type: Int, $have_store: Boolean)
                    {
                        area(
                            type: $type,
                            have_store: $have_store
                        )
                        {
                            id,
                            db_id,
                            name,
                        }
                    }',
            'variables' => [
                'type' => self::LOCATION_TYPE,
                'have_store' => true
            ]
        ];
    }

    /**
     * @return mixed|void
     */
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
