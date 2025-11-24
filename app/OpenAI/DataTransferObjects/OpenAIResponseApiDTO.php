<?php

namespace App\OpenAI\DataTransferObjects;
class OpenAIResponseApiDTO
{
  public $id;
  public $object;
  public $createdAt;
  public $status;
  public $background;
  public $error;
  public $incompleteDetails;
  public $instructions;
  public $maxOutputTokens;
  public $model;
  public $output;
  public $parallelToolCalls;
  public $previousResponseId;
  public $reasoning;
  public $serviceTier;
  public $store;
  public $temperature;
  public $textFormat;
  public $toolChoice;
  public $tools;
  public $topP;
  public $truncation;
  public $usage;
  public $user;
  public $metadata;
  public $outputContentText;

  public function __construct(array $response)
  {
    $this->id = $response['id'] ?? null;
    $this->object = $response['object'] ?? null;
    $this->createdAt = $response['created_at'] ?? null;
    $this->status = $response['status'] ?? null;
    $this->background = $response['background'] ?? null;
    $this->error = $response['error'] ?? null;
    $this->incompleteDetails = $response['incomplete_details'] ?? null;
    $this->instructions = $response['instructions'] ?? null;
    $this->maxOutputTokens = $response['max_output_tokens'] ?? null;
    $this->model = $response['model'] ?? null;
    $this->output = $response['output'] ?? [];
    $this->outputContentText = json_decode($response['output'][0]['content'][0]['text'] ?? [], true);
    $this->parallelToolCalls = $response['parallel_tool_calls'] ?? null;
    $this->previousResponseId = $response['previous_response_id'] ?? null;
    $this->reasoning = $response['reasoning'] ?? [];
    $this->serviceTier = $response['service_tier'] ?? null;
    $this->store = $response['store'] ?? null;
    $this->temperature = $response['temperature'] ?? null;
    $this->textFormat = $response['text']['format']['type'] ?? null;
    $this->toolChoice = $response['tool_choice'] ?? null;
    $this->tools = $response['tools'] ?? [];
    $this->topP = $response['top_p'] ?? null;
    $this->truncation = $response['truncation'] ?? null;
    $this->usage = $response['usage'] ?? [];
    $this->user = $response['user'] ?? null;
    $this->metadata = $response['metadata'] ?? [];
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'object' => $this->object,
      'created_at' => $this->createdAt,
      'status' => $this->status,
      'background' => $this->background,
      'error' => $this->error,
      'incomplete_details' => $this->incompleteDetails,
      'instructions' => $this->instructions,
      'max_output_tokens' => $this->maxOutputTokens,
      'model' => $this->model,
      'output' => $this->output,
      'output_content_text' => $this->outputContentText,
      'parallel_tool_calls' => $this->parallelToolCalls,
      'previous_response_id' => $this->previousResponseId,
      'reasoning' => $this->reasoning,
      'service_tier' => $this->serviceTier,
      'store' => $this->store,
      'temperature' => $this->temperature,
      'text_format' => $this->textFormat,
      'tool_choice' => $this->toolChoice,
      'tools' => $this->tools,
      'top_p' => $this->topP,
      'truncation' => $this->truncation,
      'usage' => $this->usage,
      'user' => $this->user,
      'metadata' => $this->metadata,
    ];
  }
}
