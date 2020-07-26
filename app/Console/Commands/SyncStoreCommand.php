<?php

namespace App\Console\Commands;

use App\Models\Store;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;

class SyncStoreCommand extends Command
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync stores from ERP';

    /**
     * SyncStoreCommand constructor.
     * @param Logger $logger
     * @param Client $guzzleClient
     */
    public function __construct(Logger $logger, Client $guzzleClient)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $graphqlEndpoint = env('GRAPHQL_ENDPOINT');
        $options = [
            'headers' => [
                'content-type' => 'application/json'
            ],
            'json' => [
                'query'=> '
                    query GetStores($id: Int, $limit: Int, $page: Int, $order_by: StoreOrderByEnum, $order_type: OrderTypeEnum, $latitude: Float, $longitude: Float, $distance: Float, $search: String, $province_id: Int, $district_id: Int, $bought: Boolean, $is_clinic: Boolean)
                    {
                        store(
                            id: $id,
                            limit: $limit,
                            page: $page,
                            order_by: $order_by,
                            order_type: $order_type,
                            latitude: $latitude,
                            longitude: $longitude,
                            distance: $distance,
                            search: $search,
                            province_id: $province_id,
                            district_id: $district_id,
                            bought: $bought,
                            is_clinic: $is_clinic
                        )
                        {
                            id,
                            db_id,
                            name,
                            description,
                            status,
                            province_id,
                            province,
                            district_id,
                            district,
                            ward_id,
                            ward,
                            address,
                            images
                        }
                    }',
                'variables'=> [
                ]
            ]
        ];

        $response = $this->guzzleClient->post($graphqlEndpoint, $options);
        $contents = json_decode($response->getBody()->getContents(), true);
        $stores = $contents['data']['store'];
        foreach ($stores as $store) {
            try{
                Store::updateOrCreate(
                    [
                        'id' =>  $store['db_id']
                    ],
                    [
                        'id' => $store['db_id'],
                        'name' => $store['name'],
                        'description' => $store['description'] ?? '',
                        'status' => $store['status'],
                        'province_id' => $store['province_id'],
                        'province' => $store['province'],
                        'district_id' => $store['district_id'],
                        'district' => $store['district'],
                        'ward_id' => $store['ward_id'],
                        'ward' => $store['ward'],
                        'address' => $store['address'],
                        'images' => json_encode($store['images']),
                        'level' => $store['level'] ?? '',
                    ]
                );
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->error(json_encode($store));
            }
            $this->logger->info('Synced store ' . json_encode($store));
        }
    }
}
