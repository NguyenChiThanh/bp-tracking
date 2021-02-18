<?php


namespace App\Services;


use App\Constraints\CampaignStatusConstraint;
use App\Models\Booking;
use App\Models\Campaign;
use Illuminate\Support\Facades\Log;
use Exception;
use DB;

class CampaignService
{
    /**
     * @param $data
     * @param  null  $id
     * @return Campaign
     * @throws Exception
     */
    public function storeCampaign($data, $id = null)
    {
        try {
            DB::beginTransaction();
            $positionList = $data['position_list'];

            $campaignArr = [
                'name' => $data['name'],
                'contract_code' => $data['contract_code'],
                'license_code' => $data['license_code'],
                'brand_id' => !empty($data['brand']) ? $data['brand']['id'] : null,
                'days_diff' => !empty($data['days_diff']) ? $data['days_diff'] : (round(($data['to_ts'] - $data['from_ts']) / (24*60*60*1000), 0, PHP_ROUND_HALF_UP) + 1),
                'position_price' => floatval($data['position_price']),
                'position_list' => json_encode(array_column($positionList, 'id')),
                'from_ts' => $data['from_ts'],
                'to_ts' => $data['to_ts'],
                'discount_type' => $data['discount_type'],
                'discount_value' => floatval($data['discount_value']),
                'discount_max' => floatval($data['discount_max']),
                'total_discount' => floatval($data['total_discount']),
                'total_price' => floatval($data['total_price']),
                'created_by' => $data['user_id'],
                'status' => $data['status']['value'] ? $data['status']['value'] : CampaignStatusConstraint::STATUS_RESERVED,
            ];

            if ($id) {
                $campaign = Campaign::find($id);
                $campaign->fill($campaignArr);

                // Delete old to sync new booking
                Booking::query()
                    ->where('campaign_id', $id)
                    ->delete();
            } else {
                $campaign = new Campaign($campaignArr);
            }
            $campaign->save();

            foreach ($positionList as $pos) {
                $booking = new Booking([
                    'campaign_id' => $campaign->id,
                    'position_id' => $pos['id'],
                    'buffer_ts' => $pos['buffer_days'] * 24 * 3600,
                    'from_ts' => $data['from_ts'],
                    'to_ts' => $data['to_ts'],
                ]);
                $booking->save();
            }

            DB::commit();

            return $campaign;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }
}
