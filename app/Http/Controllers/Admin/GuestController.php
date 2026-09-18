<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    
    public function guestVoucherBookOrders(Request $request)
    {
        

        if ($request->ajax()) {
            $orders = Order::where('payment_method', 'stripe')
                ->select('id','user_id','order_id','amount','status','created_at', 'delivery_option', 'delivery_charge', 'first_name', 'last_name','admin_charge')
                ->where('status', '0')
                ->when($request->delivery_option, function ($query) use ($request) {
                    return $query->where('delivery_option', $request->delivery_option);
                })
                ->orderBy('id', 'DESC');

            return DataTables::eloquent($orders)
                ->addColumn('donor', function ($order) {
                    if ($order->user_id && $order->user) {
                        if ($order->user->profile_type == 'Company') {
                            return $order->user->surname;
                        }
                        return $order->user->name . ' ' . $order->user->surname;
                    }
                    // Guest user fallback
                    return trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? ''));
                })
                ->addColumn('status_text', function ($order) {
                    return $order->status == 0 ? 'New Order' : '';
                })
                ->addColumn('barcode', function ($order) {
                    return '<a href="' . route('barcode', $order->id) . '"><i class="fa fa-eye"></i></a>';
                })
                ->addColumn('action', function ($order) {
                    return '
                    <a href="' . route('singleorder', $order->id) . '"><i class="fa fa-eye"></i></a>
                    <a href="' . route('donor.vorderEdit', $order->id) . '"><i class="fa fa-edit"></i></a>
                    ';
                })
                ->editColumn('created_at', function ($order) {
                    return $order->created_at->format('d/m/Y');
                })
                ->rawColumns(['barcode', 'action'])
                ->make(true);
        }

        return view('voucher.guest_voucher_book_order');
    }


}
