<?php
namespace App\OpenAI\Traits;

use App\Models\WhatsAppChat;
use App\OpenAI\DataTransferObjects\OpenAICompletionApiDTO;
use Illuminate\Support\Facades\Http;

trait OpenAICompletion
{
  public function generateOpenAICompletionResponse($user, $message)
  {
    $chat_history = $this->getChatHistory($user);
    $chat_history[] =
      [
        'role' => 'user',
        'content' => $message
      ]
    ;
    array_unshift($chat_history, [
      'role' => 'developer',
      'content' => $this->systemPrompt ?? 'You are a helpful assistant.'
    ]);

    $payload = [
      'model' => $this->model,
      'messages' => $chat_history,
      'temperature' => 0.7,
    ];
    info('OpenAI Completion Payload', $payload);
    $response = $this->callApi(config('constant.open_ai.paths.completions'), $payload);
    return new OpenAICompletionApiDTO($response);
    // return $response;
  }

  public function generateOpenAiSummarizesResponse($userMessage): OpenAICompletionApiDTO
  {
      $messages = [
          [
              'role' => 'system',
              'content' =>  $this->summarizingSystemPrompt
          ],
          [
              'role' => 'user',
              'content' => $userMessage
          ]
      ];
    $response = $this->callApi(config('constant.open_ai.paths.completions'), [
      'model' => $this->model,
       'messages' => $messages,
    ]);
    return new OpenAICompletionApiDTO($response);
  }

  private function getChatHistory($user)
  {
    $chatData = $user->whatsAppChats()
      ->select('request', 'response', 'created_at')
//      ->where('created_at', '>=', now()->subMinutes(10))
      ->orderBy('created_at', 'desc')
      ->limit(5)
      ->get()
      ->sortBy('created_at')
      ->values();
    $chatData = $chatData->map(function ($chat) {
      return [
        [
          'role' => 'user',
          'content' => $chat->request
        ],
        [
          'role' => 'assistant',
          'content' => $chat->response
        ]
      ];
    });
    return $chatData->flatten(1)->toArray();
  }
}
