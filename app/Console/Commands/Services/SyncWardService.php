<?php


namespace App\Console\Commands\Services;

use App\Models\Ward;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Log\Logger;

class SyncWardService extends BaseSyncService implements SyncInterface
{
    const LOCATION_TYPE = 2;

    /**
     * SyncWardService constructor.
     * @param Logger $logger
     * @param Client $guzzleClient
     */
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
                    query GetWards ($type: Int, $have_store: Boolean)
                    {
                        area(
                            type: $type,
                            have_store: $have_store
                        )
                        {
                            id,
                            db_id,
                            name,
                            parentArea{id, db_id, name}
                        }
                    }',
            'variables' => [
                'type' => self::LOCATION_TYPE,
                'have_store' => true
            ]
        ];
    }

    public function syncData()
    {
        $response = $this->guzzleClient->post(
            $this->graphqlEndpoint,
            $this->options
        );
        $contents = json_decode($response->getBody()->getContents(), true);
        $wards = $contents['data']['area'];
        foreach ($wards as $ward) {
            try {
                Ward::updateOrCreate(
                    [
                        'id' => $ward['db_id']
                    ],
                    [
                        'id' => $ward['db_id'],
                        'name' => $ward['name'],
                        'district_id' => $ward['parentArea']['db_id']
                    ]
                );
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->error(json_encode($ward));
            }
            $this->logger->info('Synced ward ' . json_encode($ward));
        }
    }
}
