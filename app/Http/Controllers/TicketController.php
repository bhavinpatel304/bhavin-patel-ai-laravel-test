<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Jobs\ClassifyTicket;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function list()
    {
        if(request()->has('search') && !empty(request('search'))) {
            $search = request()->input('search');
            $tickets = Ticket::
                             select('id', 'category', 'confidence', 'explanation', 'note', 'is_classified')
                             ->where('id', 'like', "%{$search}%")
                             ->orWhere('category', 'like', "%{$search}%")
                             ->orWhere('explanation', 'like', "%{$search}%")
                             ->orWhere('note', 'like', "%{$search}%")
                             ->orderBy('created_at','desc')
                             ->paginate(10);
            return response()->json([
                'tickets' => $tickets,
                'categories' => Ticket::CATEGORIES,
                'statuses' => TicketStatus::values(),
            ]);
        }
        else
            $tickets = Ticket::
                            select('id', 'category', 'confidence', 'explanation', 'note', 'is_classified')
                            ->orderBy('created_at','desc')
                            ->paginate(10); 
        return response()->json([
            'tickets' => $tickets,
            'categories' => Ticket::CATEGORIES,
            'statuses' => TicketStatus::values(),
        ]);
    }

    public function detail($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            return response()->json([
                'ticket' => $ticket,
                'statuses' => TicketStatus::values(),
                'categories' => Ticket::CATEGORIES,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ticket not found.',
                'error' => 'No ticket exists with ID: ' . $id
            ], 404);
        }
    }

    public function update($id)
    {
        try{
            $ticket = Ticket::findOrFail($id);                
            $data = request()->validate([
                'status' => ['required', 'string', Rule::in(TicketStatus::values())],
                'category' => ['required', 'string', 'in:' . implode(',', Ticket::CATEGORIES)],
                'note' => 'required|string',
            ]);
            $data['category_changed'] = true;
            $ticket->update($data);

            return response()->json([
                'message' => 'Ticket updated successfully',
                'ticket' => $ticket
            ]);
                
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ticket not found.',
                'error' => 'No ticket exists with ID: ' . $id
            ], 404);
        } catch (ValidationException $data) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $data->errors()
            ], 422);
        }
    }

    public function updateCategory($id)
    {
        try{
            $data = request()->validate([
                'category' => ['required', 'string', 'in:' . implode(',', Ticket::CATEGORIES)],
            ]);
            $ticket = Ticket::findOrFail($id);
            if($ticket){
                $ticket->update([
                    'category' => $data['category'],
                    'category_changed' => true
                ]);
            }
            else {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }
            return response()->json(['ticket' => $ticket]);
         } catch (ValidationException $data) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $data->errors()
            ], 422);
        }
    }

    public function create()
    {
        try{
            $data = request()->validate([
                'subject' => 'required|string|max:191',
                'body' => 'required|string|max:191',
                'status' => ['required', 'string', Rule::in(TicketStatus::values())],
                'category' => 'required|string|max:191',
                'note' => 'required|string|max:191',
                'explanation' => 'required|string|max:191',
                'confidence' => 'required|numeric',
                'note' => 'required|string|max:191',
            ]);
            $ticket = Ticket::create($data);
            return response()->json([
                'message' => 'Ticket created successfully',
                'ticket' => $ticket
            ], 201);
        } catch (ValidationException $data) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $data->errors()
            ], 422);
        }        
    }

    public function dispatchQueueJob($id)
    {      
        try {
            $ticket = Ticket::findOrFail($id);
            if($ticket->is_classified){
                return response()->json(['message' => 'Method not allowed'], 403);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ticket not found.',
                'error' => 'No ticket exists with ID: ' . $id
            ], 404);
        }

        $isClassifyEnabled = env('OPENAI_CLASSIFY_ENABLED',false);

        try {
            ClassifyTicket::dispatch($ticket,$isClassifyEnabled);
            return response()->json(['message' => 'Ticket classification queued.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
        
    }
}
