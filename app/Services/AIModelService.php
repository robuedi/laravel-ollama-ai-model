<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Storage;
use Log;

class AIModelService
{
    public function __construct(private string $clientPath = '')
    {
        $this->clientPath = config('ai.text_client_path');
    }

    public function pullModel(string $name, bool $stream = true): Response|StreamedResponse
    {
        //if stream is disabled
        if(!$stream)
        {

            $response = Http::timeout(60)->post($this->clientPath.'/api/pull', [
                'name' => $name,
                'stream' => false
            ]);

            return $response;
        }

        return $this->streamCall(fn() => Http::withOptions(['stream' => true])
            ->post($this->clientPath.'/api/pull', ['name' => $name])
            ->body()
        );
    }

    /**
     * string $name e.g. mistral-houses
     * string $system e.g. You are a real estate assistant with built-in knowledge of our properties. Use the provided knowledge base to answer questions accurately.
     * string $trainingData e.g. Available properties:\n1. 123 Oak St - $450,000, 3 bed, 2 bath, 2000 sqft, Type: Single Family, Status: Available\nModern home with updated kitchen and large backyard.\n\n2. 456 Maple Ave - $380,000, 2 bed, 2 bath, 1500 sqft, Type: Condo, Status: Available\nLuxury condo in downtown area with parking.\n\n
     */
    public function createModel(string $fromModel, string $modelName, float $temperature, string $system, string $trainingData): Response
    {
        $response = Http::timeout(60)->post($this->clientPath.'/api/create', [
            'name' => $modelName,
            'from' => $fromModel,
            'system' => $system,
            'template' => "{{ .System }}\n\nKnowledge Base:\n{{ .PropertyData }}\n\nQuestion: {{ .Prompt }}",
            'parameter'=> [
               'temperature' => $temperature,
               'num_ctx' => 4096
            ],
            'propertyData'=> $trainingData,
            'stream' => false
        ]);

        return $response;
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

    public function generate(string $model, string $prompt, bool $stream = false): Response|StreamedResponse
    {
        if(!$stream)
        {

            $response = Http::timeout(60)->post($this->clientPath.'/api/generate', [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false
            ]);

            return $response;
        }
        

        return $this->streamCall(fn() => Http::withOptions(['stream' => true])
            ->post($this->clientPath.'/api/generate', [
                'model' => $model,
                'prompt' => $prompt,
            ])
            ->body()
        );
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

    private function streamCall($streamCall)
    {
        $response = new StreamedResponse(function () use ($streamCall) {
            $stream = $streamCall();
            // Read the response line by line
            foreach (explode("\n", $stream) as $line) {
                if (!empty($line)) {
                    echo "data: " . $line . "\n\n";
                    ob_flush();
                    flush();
                }
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
