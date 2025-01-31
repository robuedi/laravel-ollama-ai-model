<?php

namespace App\Services;

// 6️⃣	/api/create	Create a custom model
// 8️⃣	/api/push	Upload a model
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AIModelService
{
    public function __construct(private string $clientPath = '')
    {
        $this->clientPath = config('ai.text_client_path');
    }

    public function pullModel(string $name): void
    {
        //TODO should stream, stream: true
        $response = Http::timeout(60)->post($this->clientPath.'/api/pull', [
            'name' => $name,
            'stream' => false
        ]);
    }

    public function deleteModel(string $name): Response
    {
        $response = Http::timeout(60)->delete($this->clientPath.'/api/delete', [
            'name' => $name,
            'stream' => false
        ]);

        return $response;
    }

    public function showModel(string $name): Response
    {
        $response = Http::timeout(60)->post($this->clientPath.'/api/show', [
            'name' => $name,
            'stream' => false
        ]);

        return $response;
    }

    public function generate(string $model, string $prompt): Response
    {
        $response = Http::timeout(60)->post($this->clientPath.'/api/generate', [
            'model' => $model,
            'prompt' => $prompt,
            'stream' => false
        ]);
        
        return $response;
    }

    public function tags(): Response
    {
        $response = Http::timeout(60)->get($this->clientPath.'/api/tags');
        
        return $response;
    }

    public function models(): Response
    {
        $response = Http::timeout(60)->get($this->clientPath.'/v1/models');
        
        return $response;
    }
}
