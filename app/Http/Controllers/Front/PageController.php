<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    
    public function aboutUs()
    {
        // Display the About Us page
        return view('front.pages.about-us');
    }

    public function termsOfService()
    {
        // Display the Terms of Service page
        return view('front.pages.terms-of-service');
    }

    public function privacyPolicy()
    {
        // Display the Privacy Policy page
        return view('front.pages.privacy-policy');
    }
}
