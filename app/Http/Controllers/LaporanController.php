<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $topCustomers = DB::table('customers')
            ->join('orders', 'customers.customer_id', '=', 'orders.customer_id')
            ->join('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->select(
                'customers.company_name',
                'customers.country',
                DB::raw('SUM(order_details.unit_price * order_details.quantity) as total_pembelian')
            )
            ->groupBy('customers.customer_id', 'customers.company_name', 'customers.country')
            ->orderBy('total_pembelian', 'desc')
            ->limit(10)
            ->get();

        return view('laporan', compact('topCustomers'));
    }
}
