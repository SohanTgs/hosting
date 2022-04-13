<?php

namespace App\Http\Controllers;
use App\Models\AdminNotification;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Domain; 
use Image;

class SiteController extends Controller
{
    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }

    public function index(){  
        $count = Page::where('tempname',$this->activeTemplate)->where('slug','home')->count();
        if($count == 0){
            $page = new Page();
            $page->tempname = $this->activeTemplate;
            $page->name = 'HOME';
            $page->slug = 'home';
            $page->save();
        }

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }
        
        $pageTitle = 'Home';
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();
        return view($this->activeTemplate . 'home', compact('pageTitle','sections'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',$this->activeTemplate)->where('slug',$slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle','sections'));
    }


    public function contact() 
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact',compact('pageTitle'));
    }


    public function contactSubmit(Request $request)
    {

        $attachments = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);


        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();
        
        $notify[] = ['success', 'ticket created successfully!'];

        return redirect()->route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return redirect()->back();
    }

    public function blogDetails($id,$slug){
        $blog = Frontend::where('id',$id)->where('data_keys','blog.element')->firstOrFail();
        $pageTitle = $blog->data_values->title;
        return view($this->activeTemplate.'blog_details',compact('blog','pageTitle'));
    }


    public function cookieAccept(){
        session()->put('cookie_accepted',true);
        $notify[] = ['success','Cookie accepted successfully'];
        return back()->withNotify($notify);
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30; 
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function productConfigure($id){
  
        $product = Product::where('status', 1)->whereHas('price', function($price){
                    $price->filter($price);
                }) 
                ->whereHas('serviceCategory', function($category){
                    $category->where('status', 1);
                })
                ->with('getConfigs.activeGroup.activeOptions.activeSubOptions.getOnlyPrice')
                ->findOrFail($id); 
     
        $pageTitle = 'Product Configure';

        $domains = [];    

        if($product->domain_register){
            $domains = Domain::active()->latest()->get();
        }

        \Session::flash('previous', $id); 

        return view($this->activeTemplate . 'product_configure', compact('product', 'pageTitle', 'domains', 'domains'));
    } 

    public function demo(Request $request){
   
        $this->validate($request, [
            'file' => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:4096',
        ]); 
        
        $general = GeneralSetting::first();
        
        $image = $request->file('file');
        $input['file'] = time().'.'.$image->getClientOriginalExtension();
        $imgFile = Image::make($image->getRealPath());
       
        list($width, $height) = getimagesize($image);

        // $imgFile->blur(30); 
        // $imgFile->brightness(35);

        // $imgFile->circle(70, 150, 100, function ($draw) {
        //     $draw->border(5, '000000');
        // });

        $imgFile->text($general->sitename, 15, 15, function($font) { 
            $font->size(35);  
            $font->color('#ffffff');  
            $font->align('left');  
            $font->valign('bottom');  
            $font->angle(90); 
        });

        $mWidth = $width/2;
        $mHeight = $height/2;

        $imgFile->text($general->sitename, $mWidth, $mHeight, function($font) { 
            $font->size(35); 
            $font->color('#ffffff');  
            $font->align('center');  
            $font->valign('middle');  
            $font->angle(90);
        });

        $width -= 15;
        $height -= 20;

        $imgFile->text($general->sitename, $width, $height, function($font) { 
            $font->size(35); 
            $font->color('#ffffff');  
            $font->align('right');  
            $font->valign('top');  
            $font->angle(90);
        });

        $imgFile->save('assets/demo/'.$input['file']); 

        return back()
        	->with('success','File successfully uploaded.')
        	->with('fileName',$input['file']);         
    }

 

}


