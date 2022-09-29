<?php

namespace App\Http\Controllers;

use App\ContactUs;
use App\Menu;
use App\News;
use App\Product;
use App\Project;
use App\Slider;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    //

    public function AddMenu(){
        return view('manage.menu.create')
        ->with('bheading', 'Add Menu')
        ->with('breadcrumb', 'Add Menu');
    }

    public function createMenu(Request $request){
        $valid = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if($valid->fails()){
            \Session::flash('alert', 'error');
            \Session::flash('message','Some fields are missing');
            return back();
        }
         Menu::create([
            'name' => $request->name
         ]);
         \Session::flash('alert', 'success');
         \Session::flash('message','Menu created successfully');
         return back();

    }

    public function MenuIndex(){
        return view('manage.menu.index')
        ->with('bheading', 'Menu List')
        ->with('breadcrumb', 'Menu List')
        ->with('menu', Menu::get());
    }


    public function EditMenu($id){ 
        $id = Menu::where('id', decrypt($id))->first();
        
        return view('manage.menu.edit')
        ->with('bheading', 'Edit Menu')
        ->with('breadcrumb', 'Edit Menu')
        ->with('menu', $id);
    }

    public function updateMenu(Request $request, $id){
        $id = Menu::where('id', decrypt($id))->first()
        ->update(['name' => $request->name]);
        \Session::flash('alert', 'success');
        \Session::flash('message','Menu updated successfully');
        return back();
    }

    public function SliderIndex(){
        return view('manage.slider.index')
        ->with('bheading', 'Sliders')
        ->with('breadcrumb', 'Sliders')
        ->with('sliders', Slider::get());
    }

    public function CreateSlider(){
        return view('manage.slider.create') 
         ->with('bheading', 'Create Slider')
        ->with('breadcrumb', 'Create Slider');

    }

    public function StoreSlider(Request $request){
        $valid = validator::make($request->all(), [
            'image' => 'required'
        ]);
       
        if($valid->fails()){
            \Session::flash('alert', 'error');
            \Session::flash('message','Some fields are missing');
            return back(); 
        }
        if($request->file('image')){
            $image = $request->file('image');
            $ex = $image->getClientOriginalExtension();
            $fileName = time().'.'.$ex;
            Image::make(request()->file('image'))->resize(1250,460)->save('images/sliders/'.$fileName);
        }
            Slider::create([
                'image' => $fileName,
                'name' => $request->name
            ]);
            \Session::flash('alert', 'success');
            \Session::flash('message','Slider Added Successfully');
            return back(); 
        

    }

    public function DeleteSlider($id){
        $id = Slider::where('id', decrypt($id))->first();
        $id->delete();
        \Session::flash('alert', 'error');
        \Session::flash('message','Slider deleted Successfully');
        return back();
    }

    public function Pages($slug){
        switch($slug){
            case "home":
                return app('App\Http\Controllers\HomeController')->Index();
                break;
            case "projects":
             return view('users.pages.projects')->with('projects', Project::inRandomOrder()->get())
                ->with('recent', Project::take(5)->latest()->get());
                break;
            case "products":
                $products['prod'] = Product::take(5)->orderBy('created_at','desc')->get();
                $products['pro'] = Product::take(10)->orderBy('sale_price','ASC')->get();
               $products['products'] = Product::orderBy('created_at','desc')->paginate(20);
               return view('users.pages.products', $products);
                break;
            case "news":
               return view('users.pages.news')->with('news', News::latest()->get())
               ->with('recent', News::latest()->take(5)->get());
            break;
            case "about": 
                return view('users.pages.about');
             break;
            case "contact":
                return view('users.pages.contact')
                ->with('contact', ContactUs::latest()->first());
                break;
            default:
            return 404;
            break;
        }

    }

    public function newsDetails($id){
        return view('users.pages.news-details')
        ->with('news', News::where('id', decrypt($id))->first())
        ->with('recent', News::latest()->take(5)->get());
    }

}
