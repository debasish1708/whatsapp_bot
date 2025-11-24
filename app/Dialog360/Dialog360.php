<?php
namespace App\Dialog360;

use App\Dialog360\Traits\Dialog360WhatsApp;
use Illuminate\Support\Facades\Http;
class Dialog360
{
  use Dialog360WhatsApp;
  private $apiKey;
  private $baseUrl;

  public function __construct()
  {
    $this->apiKey = config('constant.360dialog.api_key');
    $this->baseUrl = config('constant.360dialog.base_url');
  }

  private function callApi($path, $data = [])
  {
    $url = $this->baseUrl . $path;

    $response = Http::withHeaders([
      'D360-API-KEY' => $this->apiKey,
      'Content-Type' => 'application/json'
    ])->post($url, $data);

    if ($response->failed()) {
      info('360Dialog API Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      $response->throw();
    }

    return $response->json();
  }

  private function getMediaApi($path)
  {

    $response = Http::withHeaders([
      'D360-API-KEY' => $this->apiKey,
      'Content-Type' => 'application/json'
    ])->get($this->baseUrl . $path);

    if ($response->failed()) {
      info('360Dialog Audio API Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      $response->throw();
    }

    return $response->json();
  }

  private function downloadMediaFile($mediaUrl)
  {
    $url = $this->baseUrl . $mediaUrl;

    info('Final media download URL: ' . $url);

    $response = Http::withHeaders([
      'D360-API-KEY' => $this->apiKey,
      'Content-Type' => 'application/json'
      ])->get($url);


    if ($response->failed()) {
      info('360Dialog Audio Download Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      $response->throw();
    }

    return $response->body();
  }
  public function verifyWebhook($request): bool
  {
    return true;
    // need to set this.
    // return ($request->webhook_token === config('services.360dialog.webhook_token'));
  }

  public function renderTemplate(string $templateName, array $variables): string
  {
    $template = config("constant.whatsapp_templates.$templateName.body"); // or pull from DB
    $index = 1;
    foreach ($variables as $value) {
      $template = str_replace('{{' . $index . '}}', $value, $template);
      $index++;
    }

    return $template;
  }



}
