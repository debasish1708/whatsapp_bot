<?php

namespace App\Jobs;

use App\Models\RestaurantMenuItem;
use App\Models\RestaurantOffer;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteOldRestaurantData implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $ninetyDaysAgo = Carbon::now()->subDays(90);
            DB::transaction(function() use($ninetyDaysAgo){
                RestaurantMenuItem::onlyTrashed()->where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();
                // RestaurantCart::where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();
                RestaurantOffer::onlyTrashed()->where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();
            });
        }catch(\Exception $e){
            info('Error in DeleteOldSchoolData job: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
