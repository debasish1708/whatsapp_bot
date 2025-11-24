<?php

namespace App\Actions;

use App\Models\AppConfig;
use App\Models\User;
use App\Models\WhatsAppChat;
use Illuminate\Support\Facades\Http;

class OpenAiAction
{

  public static function getResponse($userId,$message)
  {
    try{
      $system_prompt = AppConfig::where('key', 'system_prompt')->first();
      $system_prompt = $system_prompt->value ?? 'You are a polite, empathetic,
      and helpful customer support assistant. Your goal is to resolve user issues quickly and clearly while maintaining a calm and respectful tone';

      // $messages=[[
        // 'role'=>'system','content'=>$system_prompt
      // ]];
      // $chat_history=self::getChatHistory($userId,$messages);
      // dump($chat_history);

      // $payload=[
      //   'model' => self::getOpenAiModel(),
      //     'messages' => [
      //       ['role' => 'system', 'content' => $system_prompt],
      //       ['role' => 'user', 'content' => $message]
      //     ],
      //     'temperature' => 0.7,
      // ];

      $lastChat = User::findOrFail($userId)->whatsAppChats()->latest()->first();

      $payload=[
        'model' => self::getOpenAiModel(),
          'input' => [
            ['role' => 'system', 'content' => $system_prompt],
            ['role' => 'user', 'content' => $message]
          ],
      ];

      if ($lastChat) {
        $payload['previous_response_id'] = $lastChat->response_id;
      }

        $response = Http::withHeaders([
          'Authorization' => 'Bearer ' . self::getOpenAiKey(),
          'Content-Type' => 'application/json',
        ])->post(self::getOpenAiBaseUrl().self::getModelResponse(),$payload);
        if($response->failed()){
          info('Open Ai API Error', [
            'body' => $response->body()
          ]);
          $response->throw();
        }
        return $response;
    }catch(\Exception $e){
      return response()->json(['error' => 'Failed to send WhatsApp Template Message: ' . $e->getMessage()], 500);
    }
  }

  private static function getOpenAiBaseUrl()
  {
    return config('constant.open_ai.base_url');
  }
  private static function getOpenAiKey(){
    return config('constant.open_ai.api_key');
  }
  private static function getOpenAiCompletionsUrl(){
    return config('constant.open_ai.paths.completions');
  }
  private static function getOpenAiModel(){
    return config('constant.open_ai.model');
  }
  private static function getModelResponse(){
    return config('constant.open_ai.paths.model_response');
  }
  private static function getChatHistory($userId,$messages){
    $chat_data=WhatsAppChat::where('user_id', $userId)
              ->orderBy('created_at', 'desc')
              ->limit(20)
              ->get()
              ->sortBy('created_at')
              ->values();

    foreach($chat_data as $chat){
      if(!empty($chat->request) && !empty($chat->response)){
        $messages[]=[
          'role' => 'user',
          'content' => $chat->request
        ];
        $messages[]=[
          'role' => 'assistant',
          'content' => $chat->response
        ];
      }
    }
    return $messages;
  }
}
