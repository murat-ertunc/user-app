<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CustomerImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jsonData;

    /**
     * Create a new job instance.
     */
    public function __construct($jsonData)
    {
        $this->jsonData = $jsonData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customers = $this->jsonData;

        foreach ($customers as $customer) {
            Customer::firstOrCreate([
                'name' => $customer['0'],
                'email' => $customer['1'],
                'phone' => $customer['2'],
                'company' => array_key_exists(3, $customer) && filled($customer[3]) ? $customer[3] : null,
            ]);
        }
    }
}
