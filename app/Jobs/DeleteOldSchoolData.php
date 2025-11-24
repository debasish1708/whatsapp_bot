<?php

namespace App\Jobs;

use App\Models\Annoucement;
use App\Models\JobOffer;
use App\Models\SchoolClub;
use App\Models\SchoolEvent;
use App\Models\SchoolPsychologicalSupport;
use App\Models\SchoolPsychologicalSupportOfficeHour;
use App\Models\SchoolSosAlert;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteOldSchoolData implements ShouldQueue
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

                Annoucement::where('end_date', '<', Carbon::now())
                            ->whereNull('deleted_at')
                            ->delete();

                Annoucement::onlyTrashed()
                            ->where('deleted_at', '<', $ninetyDaysAgo)
                            ->forceDelete();

                SchoolClub::onlyTrashed()->where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();

                SchoolEvent::where('end_date', '<', $ninetyDaysAgo)->foreceDelete();
                SchoolEvent::onlyTrashed()
                            ->where('deleted_at', '<', $ninetyDaysAgo)
                            ->forceDelete();

                SchoolPsychologicalSupportOfficeHour::onlyTrashed()->where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();
                SchoolPsychologicalSupport::onlyTrashed()->where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();
                SchoolSosAlert::onlyTrashed()->where('deleted_at', '<', $ninetyDaysAgo)->forceDelete();


                JobOffer::where(function ($query) use ($ninetyDaysAgo) {
                    $query->whereNotNull('deleted_at')
                        ->where('deleted_at', '<', $ninetyDaysAgo);
                })->orWhere('expiry_date', '<', $ninetyDaysAgo)
                  ->forceDelete();
            });
        }catch(\Exception $e){
            info('Error in DeleteOldSchoolData job: ' . $e->getMessage());
            return;
        }

    }
}
