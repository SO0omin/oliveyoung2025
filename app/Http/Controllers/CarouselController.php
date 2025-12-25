<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carousel;

class CarouselController extends Controller
{
    public function index($page = null)
    {
        if ($page) {
            $carousels = Carousel::with('event.company')
                ->where('link_url', $page)
                ->get();
        } else {
            $carousels = Carousel::with('event.company')->get();
        }

        return view('partials.carousel', compact('carousels'));
    }
}