<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function add(Request $request)
    {
        \Log::info($request->all());
        $customer_id = session('id');

        Address::create([
            'customer_id' => $customer_id,
            'label'       => $request->label,
            'name'        => $request->name,
            'phone'       => $request->phone,
            'zipcode'     => $request->zipcode,
            'address1'    => $request->address1,
            'address2'    => $request->address2,
            'is_default'  => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'address_id' => $address->id
        ]);
    }
}