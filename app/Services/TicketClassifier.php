<?php
namespace App\Services;

use App\Models\Ticket;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Responses\CreateResponse;
use OpenAI\Testing\ClientFake;
use OpenAI\Client;
use OpenAI\Factory;

class TicketClassifier
{
    public function classify(Ticket $ticket,$isClassifyEnabled): array
    {     
        // return [
        //     'env' => config('app.open_ai_api_key'),
        //     'mail' => env('MAIL_FROM_ADDRESS'),
        // ];
        
         if (!$isClassifyEnabled) {
            // Step 1: Create a fake OpenAI client with a fake response
            $client = new ClientFake([
                CreateResponse::fake([
                    'output' => [
                        [
                            'id' => 'seg-1',
                            'type' => 'message',
                            'role' => 'assistant',
                            'content' => [
                                [
                                    'type' => 'output_text',
                                    'text' => json_encode([
                                        'explanation' => 'This ticket is about billing.',
                                        'confidence'  => 0.92,
                                        'category'    => 'Billing',
                                    ]),                                
                                    'annotations' => [],
                                    'metadata' => [],
                                    'status' => null,
                                    'end_turn' => null,
                                ]
                            ],
                        ]
                    ]
                ]),
            ]);

            // Step 2: Call responses()->create() — fake response will be returned
            $response = $client->responses()->create([
                'model' => 'gpt-3.5-turbo',
                'input' => 'Classify this ticket',
            ]);        
        }      
        else{
        
            $prompt = "You are a ticket classifier. Classify this ticket: {$ticket->body}. Analyze this ticket and return ONLY a JSON with keys: category, explanation, confidence (0-1).";
            
            $prompt = <<<EOT
                You are a ticket classifier. Analyze the given ticket information and classify it.
                Ticket details:
                - Subject: {$ticket->subject}
                - Body: {$ticket->body}
                - Note: {$ticket->note}
                - Existing Category: {$ticket->category}
                - Existing Explanation: {$ticket->explanation}
                Your task:
                - Determine the most accurate category for this ticket.
                - Provide a short explanation of why you chose this category.
                - Provide a confidence score between 0 and 1.
                Return ONLY valid JSON in this format:
                {
                "category": "string",
                "explanation": "string",
                "confidence": number
                }
                EOT;

                /*
                Below is good prompt 
                ******
            $prompt = <<<EOT
                You are a ticket classifier. Analyze the given ticket information and classify it.
                Ticket details:
                - Subject: Payment failed on checkout
                - Body: I tried paying with my credit card but it was declined twice even though I have balance
                - Note: Customer is frustrated, attempted payment multiple times.
                - Existing Category: General Inquiry
                - Existing Explanation: Customer is asking about checkout issues.
                Your task:
                - Determine the most accurate category for this ticket.
                - Provide a short explanation of why you chose this category.
                - Provide a confidence score between 0 and 1.
                Return ONLY valid JSON in this format:
                {
                "category": "string",
                "explanation": "string",
                "confidence": number
                }
                EOT;
            */

            $client = (new Factory())
                ->withApiKey(env('CUST_OPENAI_API_KEY'))
                ->make();
            
            $response = $client->responses()->create([
                'model' => 'gpt-3.5-turbo',
                'input' => $prompt,
            ]);
        }

        
        // Extract text
        $tmpArr = [];
        $returnArr = [];
        foreach ($response->output as $segment) {
            foreach ($segment->content as $contentItem) {
                if ($contentItem->type === 'output_text') {
                    $tmpArr = json_decode($contentItem->text,true);
                    if(is_array($tmpArr)){
                        $returnArr = $tmpArr;
                    }
                    
                }
            }
        }
        return $returnArr;
    
    }
}
