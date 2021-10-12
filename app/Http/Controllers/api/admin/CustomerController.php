<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    //
    public function index()
    {
        $customers = Customer::when(request()->q, function($customers) {
   			$customers = $customers->where('name', 'like', '%'. request()->q . '%');
		})->latest()->paginate(5);

        //return with Api Resource
        return new CustomerResource(true, 'List Data Customer', $customers);
    }
}
