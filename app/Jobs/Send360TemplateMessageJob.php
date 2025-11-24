<?php

namespace App\Jobs;

use App\Actions\DatabaseAction;
use App\Dialog360\Dialog360;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Send360TemplateMessageJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels,
        Dispatchable;

    /**
     * Create a new job instance.
     */
    private $dialog360Object;
    public $users;
    public $parameter;
    public $template;
    public $data;
    private $databaseAction;
    public function __construct($users, $template, $parameter, $data = [])
    {
        $this->dialog360Object = new Dialog360();
        $this->databaseAction = new DatabaseAction();
        if (!is_iterable($users)) {
            $users = [$users];
        }
        $this->users = $users;
        $this->parameter = $parameter;
        $this->data = $data;
        $this->template = $template;
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

                info("Sending announcement to user {$user->id} with mobile {$user->mobile_number}");
                $this->dialog360Object->sendTemplateWhatsAppMessage($user->mobile_number,$this->template ,$user->language_code ,$this->parameter);
                $rendered = $this->dialog360Object->renderTemplate($this->template, $this->parameter);
                $this->databaseAction->storeConversation($user, null, $rendered);
                info("Announcement sent to user {$user->id} successfully.");
            } catch (\Exception $e) {

                info("Failed to send announcement to user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
