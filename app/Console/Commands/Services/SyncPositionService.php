<?php


namespace App\Console\Commands\Services;

use DB;
use App\Models\Position;
use App\Models\Store;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncPositionService extends BaseSyncService 
{
    public function __construct($logger, $guzzleClient)
    {
        parent::__construct($logger, $guzzleClient);
    }

    /**
     * @inheritDoc
     */
    public function buildGraphqlQuery($page, $limit, $updated_at_greater_than_equal=null)
    {
        $this->logger->info("SyncPositionService get: limit " . $limit . " page " . $page. " updated_at_greater_than_equal ".$updated_at_greater_than_equal);

        return $this->options['json'] = [
            'query' => '
                query GetPositions($id: Int, $limit: Int, $page: Int, $updated_at_greater_than_equal: String, $store_id: Int)
                    {
                        marketingPOSMStorePositions(
                            id: $id,
                            store_id: $store_id,
                            updated_at_greater_than_equal: $updated_at_greater_than_equal,
                            limit: $limit,
                            page: $page,
                        )
                        {
                            id, 
                            db_id, 
                            store {
                                db_id,
                                code
                            }, 
                            status, 
                            db_status, 
                            posm_status, 
                            db_posm_status, 
                            code, 
                            name, 
                            position_code, 
                            posm_code, 
                            position_description, 
                            description, 
                            height, 
                            width, 
                            images
                        }
                    }',
            'variables' => [
                'limit' => $limit,
                'page' => $page,
                'updated_at_greater_than_equal' => $updated_at_greater_than_equal,
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function syncData($syncDate = null)
    {
        $limit = 100;
        $page = 1;
        try {
            DB::beginTransaction();
            
            do{
                $this->options['json'] = $this->buildGraphqlQuery($page, $limit, $syncDate);

                $response = $this->guzzleClient->post(
                    $this->graphqlEndpoint,
                    $this->options
                );
                $contents = json_decode($response->getBody()->getContents(), true);
                $marketingPOSMStorePositions=$contents['data']['marketingPOSMStorePositions'];
                foreach ($marketingPOSMStorePositions as $POSMPosition) {
                    $store = Store::where('code', $POSMPosition['store']['code'])->first();
                    if(\strpos($POSMPosition['code'],"-") !== false && !empty($store)){

                        $channel=\explode("-",$POSMPosition['code'])[0];
                        $positionName=$POSMPosition['store']['code'].'_'.$POSMPosition['code'];
                        $position= Position::where([
                                'store_id' => $store->id,
                                'channel' => $channel,
                                'name' => $positionName,
                            ])->first();

                        if(empty($position)){
                            $position= new Position();
                            $position->buffer_days=2;
                            $position->price = 0;
                            $position->unit = 'day';
                            $position->status = Position::AVAILABLE;
                            $position->store_id = $store->id;
                        }

                        $position->name =$positionName;
                        $position->description = empty($POSMPosition->description) ? '' : $POSMPosition->description;
                        $position->image_url = isset($POSMPosition['images'][0]) ? $POSMPosition['images'][0] : '';                       
                        $position->channel = $channel;
                        $position->width = $POSMPosition['width'];
                        $position->height = $POSMPosition['height'];
                        $position->save();
                        
                    }
                    
                }
                $page++;
                sleep(1);
            }while($limit == count($marketingPOSMStorePositions));

            DB::commit();
        } catch (\Exception $e) {
           
            Log::error($e->getMessage());
            DB::rollBack();
            dd($e->getMessage());
        }
        
    }

   
    
}
