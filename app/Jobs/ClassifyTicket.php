<?php
namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ticket $ticket;
    public bool $isClassifyEnabled;

    public function __construct(Ticket $ticket, $isClassifyEnabled)
    {
        $this->ticket = $ticket;
        $this->isClassifyEnabled = $isClassifyEnabled;
    }

    public function handle(TicketClassifier $classifier)
    {       
        $result = $classifier->classify($this->ticket,$this->isClassifyEnabled);  
        Log::info($result);
        $this->ticket->explanation = $result['explanation'];
        $this->ticket->confidence = $result['confidence'];
        if (!$this->ticket->category_changed) {
            $this->ticket->category = $result['category'];
        }

        $this->ticket->save();
    }
}
