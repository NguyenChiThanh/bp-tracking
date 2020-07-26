<?php


namespace App\Console\Commands\Services;

use App\Models\Brand;

class SyncBrandService extends BaseSyncService implements SyncInterface
{
    public function __construct($logger, $guzzleClient)
    {
        parent::__construct($logger, $guzzleClient);
        $this->buildGraphqlQuery();
    }

    public function buildGraphqlQuery()
    {
        return $this->options['json'] = [
                'query' => '
                    query GetBrands($id: Int)
                    {
                        brand(id:$id)
                        {
                            id, db_id, name, products{id, db_id, name}
                        }
                    }
                '
            ];
    }

    public function syncData()
    {
        $response = $this->guzzleClient->post(
            $this->graphqlEndpoint,
            $this->options
        );
        $contents = json_decode($response->getBody()->getContents(), true);
        $brands = $contents['data']['brand'];
        foreach ($brands as $brand) {
            try {
                Brand::updateOrCreate(
                    [
                        'id' => $brand['db_id']
                    ],
                    [
                        'id' => $brand['db_id'],
                        'name' => $brand['name'],
                        'products' => json_encode($brand['products']),
                    ]
                );
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->error(json_encode($brand));
            }
            $this->logger->info('Synced store ' . json_encode($brand));
        }
    }
}
