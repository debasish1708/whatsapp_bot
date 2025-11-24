<?php
namespace App\OpenAI\Traits;

use App\OpenAI\DataTransferObjects\OpenAIResponseApiDTO;

trait OpenAIResponse
{
  public function generateOpenAiResponse($userMessage, $previousResponseId = null): OpenAIResponseApiDTO
  {
    $response = $this->callApi(config('constant.open_ai.paths.model_response'), [
      'model' => $this->model,
      'input' => [
        ['role' => 'system', 'content' => $this->systemPrompt],
        ['role' => 'user', 'content' => $userMessage]
      ],
      'previous_response_id' => $previousResponseId,
    ]);
    return new OpenAIResponseApiDTO($response);
  }
}
