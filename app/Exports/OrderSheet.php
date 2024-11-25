<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class OrderSheet implements FromView
{
    public function __construct($data)
    {
        $this->data = $data;
      
    }
    /**
    * @return \Illuminate\Support\Collection
    */
   
    public function view(): View
    {
        // 1
        set_time_limit(30000);
          return view('sheet_orders', [
            'orders' => $this->data,
            'mytime' => Carbon::now()
        ]);
    }
}
