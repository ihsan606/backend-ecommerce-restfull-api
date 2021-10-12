<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(){
        //get data slider
        $slider = Slider::latest()->get();

        return new SliderResource(true,'List Data Slider',$slider);
    }
}
