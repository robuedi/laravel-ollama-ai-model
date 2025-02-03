<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenericListingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AIModelService;
use Http;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Log;

class OllamaController extends Controller
{
    public function __construct(private AIModelService $aiModelService)
    {
        // the user needs to be logged in for these methods to be accessed
        // $this->middleware('auth:api');
    }

    /**
     * Pull Model
     *
     * @unauthenticated
     */
    public function pullModel(Request $request)
    {
        $request->validate([
            /**
             * Model Name.
             *
             * @example mistral
             */
            'name' => 'in:'.config('ai.ollama_allowed_models'),

            /**
             * Stream the pull request.
             *
             * @example true
             */
            'stream' => 'bool',
        ]);

        $response = $this->aiModelService->pullModel(name: $request->input('name'), stream: $request->input('stream') ? true : false);

        return $response;
    }

    /**
     * Delete Model
     *
     * @unauthenticated
     */
    public function deleteModel(Request $request)
    {
        $request->validate([
            /**
             * Model Name.
             *
             * @example mistral
             */
            'name' => 'required|string',
        ]);

        $response = $this->aiModelService->deleteModel(name: $request->input('name'));
        
        return response()->json($response->json())->setStatusCode($response->status());
    }

    /**
     * Show Model
     *
     * @unauthenticated
     */
    public function showModel(Request $request)
    {
        $request->validate([
            /**
             * Model Name.
             *
             * @example mistral
             */
            'name' => 'required|string',
        ]);

        $response = $this->aiModelService->showModel(name: $request->input('name'));
        
        return response()->json($response->json())->setStatusCode($response->status());
    }

    /**
     * Create Model
     *
     * @unauthenticated
     */
    public function createModel(Request $request)
    {
        $request->validate([
            /**
             * From Model (the name).
             *
             * @example mistral
             */
            'from_model' => 'required|string',

            /**
             * Model name (new model name).
             *
             * @example mistral-houses
             */
            'model_name' => 'required|string',

            /**
             * Temperature from 0.1 to 1 (0.1 neing the most precise, 1.0 being the most creative).
             *
             * @example 0.7
             */
            'temperature' => 'required|decimal:1|between:0.1,1',

            /**
             * Instructions (instructions to the new model).
             *
             * @example You are a real estate assistant with built-in knowledge of our properties. Use the provided knowledge base to answer questions accurately.
             */
            'instructions' => 'required|string',

            /**
             * Training data .
             *
             * @example Available properties:\n1. 123 Oak St - $450,000, 3 bed, 2 bath, 2000 sqft, Type: Single Family, Status: Available\nModern home with updated kitchen and large backyard.\n\n2. 456 Maple Ave - $380,000, 2 bed, 2 bath, 1500 sqft, Type: Condo, Status: Available\nLuxury condo in downtown area with parking.\n\n
             */
            'training_data' => 'required|string',
        ]);

        $response = $this->aiModelService->createModel(
            fromModel: $request->input('from_model'),
            modelName: $request->input('model_name'),
            temperature: $request->input('temperature'),
            system: $request->input('instructions'),
            trainingData: $request->input('training_data')
        );

        return response()->json($response->json())->setStatusCode($response->status());
    }

    /**
     * List of Models
     *
     * @unauthenticated
     */
    public function indexModels(Request $request)
    {
        $response = $this->aiModelService->models();
     
        return response(['response' => $response->json()])->setStatusCode($response->status());
    }

    /**
     * List of Tags 
     *
     * @unauthenticated
     */
    public function indexTags(Request $request)
    {
       
        $response = $this->aiModelService->tags();
     
        return response(['response' => $response->json()])->setStatusCode($response->status());
    }

    /**
     * Generate Text
     *
     * @unauthenticated
     */
    public function generateText(Request $request) : Response|StreamedResponse
    {
        $request->validate([
            /**
             * Model.
             *
             * @example mistral
             */
            'model' => 'required|string',

            /**
             * Prompt.
             *
             * @example How far is the moon?
             */
            'prompt' => 'required|string',

            /**
             * Stream the request.
             *
             * @example true
             */
            'stream' => 'bool',
        ]);

        $stream = $request->input('stream') ? true : false;

        $response = $this->aiModelService->generate(
            model: $request->input('model'),
            prompt: $request->input('prompt'),
            stream: $stream
        );

        if(!$stream)
        {
            return response(['response' => $response->json()])->setStatusCode($response->status());
        }

        return $response;
    }
}
