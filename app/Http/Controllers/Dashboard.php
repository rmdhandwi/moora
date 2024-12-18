<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Dashboard extends Controller
{
  public function DashboardPage()
  {
    $title = 'Dashboard';
    return Inertia::render('Admin/DashboardPage', [
      'title' => $title
    ]);
  }
}
