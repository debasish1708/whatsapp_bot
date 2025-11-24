<?php

namespace App\OpenAI\Traits;

use Illuminate\Support\Facades\Http;

trait OpenAIWhisper
{
      public function generateOpenAIWhisperResponse($fileName,$audioFilePath)
      {
        $transcription = $this->callWhisperApi(
          config('constant.open_ai.paths.whisper'),
          $fileName,
          $audioFilePath,
          ['model' => 'gpt-4o-transcribe']
        );

          return $transcription['text'];
      }
}
