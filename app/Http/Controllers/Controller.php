<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function validateContact(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|integer|unique:contacts,phone' . ($id ? ',' . $id : ''),
            'dial_code' => 'nullable|regex:/^\+\d{1,4}$/|max:5',
            'email' => 'nullable|email|max:100|unique:contacts,email' . ($id ? ',' . $id : ''),
        ]);
    }
}
