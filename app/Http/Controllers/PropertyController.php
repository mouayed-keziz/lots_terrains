<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties on dashboard.
     */
    public function index()
    {
        $properties = Property::latest()->get();
        return view('dashboard', compact('properties'));
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property)
    {
        return view('properties.show', compact('property'));
    }

    /**
     * Show the form for the specified property.
     */
    public function fillForm(Property $property)
    {
        return view('properties.fill-form', compact('property'));
    }
}
