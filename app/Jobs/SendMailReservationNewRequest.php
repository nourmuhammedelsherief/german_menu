<?php

namespace App\Jobs;

use App\Mail\ReservationEmail;
use App\Models\Admin;
use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailReservationNewRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $order , $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email , $order)
    {
        $this->order = $order;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
       
        

        \Mail::to($this->email)->send(new ReservationEmail($this->order));
    }
}
