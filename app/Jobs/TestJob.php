<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $products, $restaurant;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($restaurant, $products)
    {
        $this->products = $products;
        $this->restaurant = $restaurant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = 0;
        $tcount = 0;
        foreach ($this->products as $product) :
            if (!empty($product->foodics_id)) :
                $t = getFoodicsProduct($this->restaurant, $product->foodics_id);
                $t = json_decode($t, true);

                if (isset($t['data']['id'])) :
                    foreach ($t['data']['branches'] as $b) :

                        if ($b['id'] == '991459d2-d36e-4246-a41a-963dbc33a2d4' and !empty($b['pivot']['price'])) :
                            $product->update([
                                'price' => $b['pivot']['price']
                            ]);
                            $count++;
                        elseif($b['id'] == '991459d2-d36e-4246-a41a-963dbc33a2d4'):
                            $tcount++;
                        endif;
                    endforeach;
                endif;
            endif;
        endforeach;
        Log::alert('restaurant 1441 foodics products' . $count . ' has update price , empty : ' . $tcount);
    }
}
