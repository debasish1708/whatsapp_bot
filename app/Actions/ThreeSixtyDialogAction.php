<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class ThreeSixtyDialogAction
{

  public function sendTemplateWhatsAppMessage($number, $template_name, $language_code = 'en')
  {
    try{
        $url=$this->get360DialogBaseUrl() . config('constant.360dialog.paths.messages');
        $data = [
          'messaging_product' => 'whatsapp',
          'to' => $number,
          'type' => 'template',
          'template' => [
            "name" => $template_name,
            "language" => [
              "code" => $language_code
            ]
          ]
        ];

        $response = Http::withHeaders([
            'D360-API-KEY' => $this->get360DialogApiKey(),
            'Content-Type' => 'application/json',
        ])->post($url,$data);

        if ($response->failed()) {
        info('360 Dialog API Error', [
          'body' => $response->body()
        ]);
        $response->throw();
      }
      return $response;
    }catch(\Exception $e){
        return response()->json(['error' => 'Failed to send WhatsApp Template Message: ' . $e->getMessage()], 500);
    }
  }
  public function sendWhatsAppMessage($number, $response_message)
  {
    try {
      $url = $this->get360DialogBaseUrl() . config('constant.360dialog.paths.messages');
      $data = [
        'messaging_product' => 'whatsapp',
        'to' => $number,
        'type' => 'text',
        'text' => [
          "body" => $response_message
        ]
      ];
      $response = Http::withHeaders([
        'D360-API-KEY' => $this->get360DialogApiKey(),
        'Content-Type' => 'application/json',
      ])->post($url, $data);
      if ($response->failed()) {
        info('360 Dialog API Error', [
          'body' => $response->body()
        ]);
        $response->throw();
      }
      return $response;
    } catch (\Exception $e) {
      return response()->json(['error' => 'Failed to send WhatsApp message: ' . $e->getMessage()], 500);
    }
  }
  public function sendLocationRequest($userMobile){
    try{
      $url = $this->get360DialogBaseUrl() . config('constant.360dialog.paths.messages');
      $payload = [
        'messaging_product' => 'whatsapp',
        'recipient_type'=> 'individual',
        'type' => 'interactive',
        'to' => $userMobile,
        'interactive' => [
          'type' => 'location_request_message',
          'body' => [
            'text' => 'Please Share Your Location. You can either manually *enter an address* or *share your current location*.'
          ],
          'action' => [
            'name' => 'send_location'
          ]
        ]
      ];
      $response = Http::withHeaders([
        'D360-API-KEY' => $this->get360DialogApiKey(),
        'Content-Type' => 'application/json',
      ])->post($url, $payload);
      if ($response->failed()) {
        info('360 Dialog API Error', [
          'body' => $response->body()
        ]);
        $response->throw();
      }
      return $response;
    }catch(\Exception $e){
      return response()->json(['error' => 'Failed to send Location Request: ' . $e->getMessage()], 500);
    }
  }
  private function get360DialogBaseUrl()
  {
    return config('constant.360dialog.base_url');
  }
  private function get360DialogApiKey()
  {
    return config('constant.360dialog.api_key');
  }
}
