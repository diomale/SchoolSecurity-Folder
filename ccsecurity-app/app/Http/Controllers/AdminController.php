<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showAdminLogin()
    {
        return view(view: 'Admin.login');
    }
}
