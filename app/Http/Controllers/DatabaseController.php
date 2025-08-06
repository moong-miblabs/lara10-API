<?php

namespace App\Http\Controllers;

use App\Models\Database;

class DatabaseController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Database::create();
        return response("database created successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        Database::store();
        return response("database seeding successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        Database::destroy();
        return response("database deleted successfully");
    }
}
