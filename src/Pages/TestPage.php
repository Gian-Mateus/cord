<?php

namespace Cord\Pages;

use Illuminate\Http\Request;

class TestPage
{
    public function __invoke(Request $request)
    {
        return view('cord::pages.test');
    }
}
