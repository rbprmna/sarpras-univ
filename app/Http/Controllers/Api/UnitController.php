<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Unit::all()]);
    }
}
