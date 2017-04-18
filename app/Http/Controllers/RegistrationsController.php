<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;

class RegistrationsController extends Controller
{
    public function create()
    {
    	return view('registrations.create');
    }

    public function store(RegistrationRequest $request)
    {
        $request->persist();

		return redirect()->home();
    }
}
