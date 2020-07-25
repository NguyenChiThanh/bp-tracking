<?php

namespace App\Console\Commands;

use App\Models\Store;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncStoreCommand extends Command
{
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $graphqlEndpoint = 'https://omsapi.pharmacity.vn/graphql';
        $guzzleClient = new Client();
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

        $response = $guzzleClient->post($graphqlEndpoint, $options);
        $contents = json_decode($response->getBody()->getContents(), true);
        $stores = $contents['data']['store'];
        foreach ($stores as $store) {
            Store::create([
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
            ]);
            Log::warning('Synced store ' . json_encode($store));

        }
        // TODO: save contents to db
    }
}
