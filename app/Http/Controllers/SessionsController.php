<?php

namespace App\Http\Controllers;

class SessionsController extends Controller
{
    public function __construct()
    {
    	$this->middleware('guest', ['except' => 'destroy']);
    }
    
    public function create()
    {
		return view('sessions.create');    			
    }

    public function store()
    {
    	if (! auth()->attempt(request(['email', 'password']))) {
    		return redirect()->back()->withErrors([
    			'message' => 'Por favor verifique suas credenciais e tente novamente.'
			]);
    	}
    	
    	return redirect()->home();
    }
    
    public function destroy()
    {
    	auth()->logout();

    	return redirect()->home();
    }
}
