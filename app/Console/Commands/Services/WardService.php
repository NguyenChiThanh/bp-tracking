<?php


namespace App\Console\Commands\Services;


use App\Models\Ward;

class WardService extends BaseLocationService implements LocationServiceInterface
{
    const LOCATION_TYPE = 2;

    public function __construct($logger, $guzzleClient)
    {
        parent::__construct($logger, $guzzleClient);
        $this->buildGraphqlQuery();
    }


    public function buildGraphqlQuery()
    {
        return $this->options['json'] = [
            'query'=> '
                    query GetWards ($type: Int)
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
            'variables'=> [
                'type'=> self::LOCATION_TYPE
            ]
        ];
    }

    public function syncData()
    {
        $response = $this->guzzleClient->post(
            $this->graphqlEndpoint, $this->options
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
            }catch(\Exception $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->error(json_encode($ward));
            }
            $this->logger->info('Synced ward ' . json_encode($ward));
        }
    }
}
