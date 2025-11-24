<?php
namespace App\OpenAI;

use App\Models\AppConfig;
use App\OpenAI\DataTransferObjects\OpenAIResponseApiDTO;
use App\OpenAI\Traits\OpenAICompletion;
use App\OpenAI\Traits\OpenAIResponse;
use App\OpenAI\Traits\OpenAIWhisper;
use Illuminate\Support\Facades\Http;

class OpenAI
{
  private $apiKey, $baseUrl, $model, $systemPrompt, $summarizingSystemPrompt;
  use OpenAIResponse, OpenAICompletion, OpenAIWhisper;
  public function __construct()
  {
    $this->apiKey = config('constant.open_ai.api_key');
    $this->baseUrl = config('constant.open_ai.base_url');
    $this->model = config('constant.open_ai.model');
    $this->systemPrompt = AppConfig::getValueByKey('system_prompt');
    $this->summarizingSystemPrompt = AppConfig::getValueByKey('summarizing_system_prompt');
  }
  private function callApi($path, $data = []): array
  {
    $url = $this->baseUrl . $path;

    $response = Http::withHeader('Authorization', 'Bearer ' . $this->apiKey)
      ->withHeaders(['Content-Type' => 'application/json'])
      ->post($url, $data);

    if ($response->failed()) {
      info('OpenAI API Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      $response->throw();
    }

    return $response->json();
  }

  private function callWhisperApi($path,$fileName, $audioFilePath, $data = []){
    $url = $this->baseUrl . $path;

    $response = Http::withToken($this->apiKey)
      ->attach(
        'file',
        file_get_contents($audioFilePath),
        $fileName
      )
      ->asMultipart()
      ->post($url, $data);

    if ($response->failed()) {
      info('OpenAI API Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      $response->throw();
    }

    return $response->json();
  }

}
