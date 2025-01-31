<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenericListingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AIModelService;
use Http;
use Storage;
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
            'name' => 'in:mistral',
        ]);

        $this->aiModelService->pullModel(name: $request->input('name'));
        
        return response([])->setStatusCode(Response::HTTP_NO_CONTENT);
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
     * Show Pulled Models
     *
     * @unauthenticated
     */
    public function showModels(Request $request)
    {
       
        $response = $this->aiModelService->models();
     
        return response(['response' => $response->json()])->setStatusCode($response->status());
    }

    /**
     * Generate Text
     *
     * @unauthenticated
     */
    public function generateText(Request $request)
    {
        $request->validate([
            /**
             * Model.
             *
             * @example mistral
             */
            'model' => 'in:mistral',

            /**
             * Prompt.
             *
             * @example How far is the moon?
             */
            'prompt' => 'string',
        ]);

        $response = $this->aiModelService->generate(
            model: $request->input('model'),
            prompt: $request->input('prompt')
        );
        
        return response(['response' => $response->json()])->setStatusCode($response->status());
    }
}
