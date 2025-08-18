<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Start the query with eager loaded relationships to prevent N+1 issues
        $query = Order::with('user', 'orderDetails')->latest();

        // If a search term is present, apply the search conditions
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                // Search in the Order table
                $q->where('order_no', 'like', '%' . $searchTerm . '%')
                  
                  // Search in the related User table
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('email', 'like', '%' . $searchTerm . '%');
                                // ->orWhere('phone_number', 'like', '%' . $searchTerm . '%'); // Uncomment if you have a phone_number field
                  })
                  
                  // Search in the related OrderDetail table
                  ->orWhereHas('orderDetails', function ($detailsQuery) use ($searchTerm) {
                      $detailsQuery->where('ref_no', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Paginate the results and append the search query to pagination links
        $orders = $query->latest()->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('orderDetails', 'user');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
