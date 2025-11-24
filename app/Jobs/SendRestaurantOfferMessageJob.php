<?php

namespace App\Jobs;

use App\Constants\WhatsAppConstants;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Actions\DatabaseAction;
use App\Dialog360\Dialog360;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRestaurantOfferMessageJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels,
        Dispatchable;

    private $dialog360Object;
    public $users;
    public $offerData;
    private $databaseAction;

    /**
     * Create a new job instance.
     */
    public function __construct($users, $offerData = [])
    {
        $this->dialog360Object = new Dialog360();
        $this->databaseAction = new DatabaseAction();
        if (!is_iterable($users)) {
            $users = [$users];
        }
        $this->users = $users;
        $this->offerData = $offerData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $user) {
            try {
                if (!isset($user->mobile_number) || empty($user->mobile_number)) {
                    info("User {$user->id} does not have a mobile number.");
                    continue;
                }

                // Assuming dialog360Object is an instance of Dialog360 class
                if (!isset($this->dialog360Object)) {
                    info("Dialog360 object is not set.");
                    continue;
                }

                info("Sending Offers to user {$user->id} with mobile {$user->mobile_number}");
                $this->dialog360Object->sendRestaurantOffersDetailsToCustomer($user->mobile_number,$this->offerData, $user->language_code);
                $this->databaseAction->storeConversation($user, null, json_encode($this->offerData));
                info("Offers to user {$user->id} successfully.");
            } catch (\Exception $e) {

                info("Failed to send Offers to user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
