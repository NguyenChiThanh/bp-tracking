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
        // get total brand
        $totalBrands = $this->getTotalBrands();

        $limit = 100;
        // 422 / 210 --> 2 * 210 --> pages:  1,2,3
        $totalPages = ($totalBrands / $limit) + 1;
        $options = [
            'headers' => [
                'content-type' => 'application/json'
            ]
        ];

        for ($page = 1; $page <= $totalPages; $page++) {
            $options['json'] = $this->buildGraphqlQueryWithPagination($limit, $page);

            $response = $this->guzzleClient->post(
                $this->graphqlEndpoint,
                $options
            );
            $contents = json_decode($response->getBody()->getContents(), true);
            $brands = $contents['data']['brand'];
            foreach ($brands as $brand) {
                $this->logger->info("Brand name " . $brand['name']);
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
                $this->logger->info('Synced brand ' . json_encode($brand));
            }
        }
    }

    private function getTotalBrands()
    {
        $options = [
            'headers' => [
                'content-type' => 'application/json'
            ],
            'json' => [
                'query' => '
                    query CountBrand {
                      countBrand {
                        count
                      }
                    }
                '
            ],
        ];
        $response = $this->guzzleClient->post(
            $this->graphqlEndpoint,
            $options
        );

        $contents = json_decode($response->getBody()->getContents(), true);

        return $contents['data']['countBrand']['count'];
    }

    private function buildGraphqlQueryWithPagination($limit, $page)
    {
        $this->logger->info("limit " . $limit . " page " . $page);
        return [
            'query' => '
                query GetBrands($id: Int,  $limit: Int, $page: Int) {
                  brand(id: $id, limit: $limit, page: $page) {
                    id
                    db_id
                    name
                    products {
                      id
                      db_id
                      name
                    }
                  }
                }',
            'variables' => [
                'limit' => $limit,
                'page' => $page,
            ]
        ];

    }

}
