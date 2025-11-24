<?php

namespace App\OpenAI\DataTransferObjects;

use Illuminate\Support\Str;

class OpenAICompletionApiDTO
{
  public string $id;
  public string $object;
  public int $created;
  public string $model;
  public string $content;
  public string $finishReason;
  public int $promptTokens;
  public int $completionTokens;
  public int $totalTokens;
  public string $serviceTier;
  public string $systemFingerprint;

  public function __construct(array $response)
  {
    $this->id = $response['id'] ?? '';
    $this->object = $response['object'] ?? '';
    $this->created = $response['created'] ?? 0;
    $this->model = $response['model'] ?? '';
    $this->content = $response['choices'][0]['message']['content'] ?? '';
    $this->finishReason = $response['choices'][0]['finish_reason'] ?? '';

    $this->promptTokens = $response['usage']['prompt_tokens'] ?? 0;
    $this->completionTokens = $response['usage']['completion_tokens'] ?? 0;
    $this->totalTokens = $response['usage']['total_tokens'] ?? 0;

    $this->serviceTier = $response['service_tier'] ?? '';
    $this->systemFingerprint = $response['system_fingerprint'] ?? '';
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'object' => $this->object,
      'created' => $this->created,
      'model' => $this->model,
      'content' => $this->content,
      'finish_reason' => $this->finishReason,
      'prompt_tokens' => $this->promptTokens,
      'completion_tokens' => $this->completionTokens,
      'total_tokens' => $this->totalTokens,
      'service_tier' => $this->serviceTier,
      'system_fingerprint' => $this->systemFingerprint,
    ];
  }

  public function getContent(): string
  {
    info('OpenAI Completion Content', ['content' => $this->content]);
    if (\Illuminate\Support\Str::isJson($this->content)) {
      $data = json_decode($this->content, true);
    } else {
      $data = $this->content;
    }
    $data = $data['reply'] ?? $data['content'] ?? $this->content;
    return $data ?? 'No response available.';
  }
}
