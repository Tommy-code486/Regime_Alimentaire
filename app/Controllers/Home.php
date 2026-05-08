<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(session('accountType') === 'admin' ? site_url('admin/dashboard') : site_url('dashboard'));
        }

        return redirect()->to(site_url('login'));
    }
}
