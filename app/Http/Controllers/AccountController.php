<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    // this method will show register page
    public function register(){
        return view ('account.register');
    }

    // This method will register a user
    public function processRegister(Request $request){
        $validator= Validator::make($request->all(),[
            'name' =>'required|min:3',
            'email' =>'required|email|unique:users',
            'password' =>'required|confirmed|min:5',
            'password_confirmation' =>'required',
        ]);

        if($validator->fails()){
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }

        // Now register User
        $user= new User();
        $user->name= $request->name;
        $user->email= $request->email;
        $user->password= Hash::make($request->password);
        $user->save();
        return redirect()->route('account.login')->with('success','You have registred Successfull.');
    }

    public function login(){
        return view('account.login');
    }

    public function authenticate(Request $request){
        $validator= Validator::make($request->all(),[
            'email'=> 'required|email',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }

        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            return redirect()->route('account.profile');
        }else{
             return redirect()->route('account.login')->with('error','Either email/password is incorrect.');
        }
    }

    // This method will show user profile page
    public function profile(){
        $user= User::find(Auth::user()->id);
        // dd($user);
        return view('account.profile', [
            'user'=> $user
        ]);
    }

    // This method will update user profile
    public function updateProfile(Request $request){
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.Auth::user()->id.',id',
        ];

        if(!empty($request->image)){
            $rules['image']='image';
        }

        $validator= Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
        }

        $user= User::find(Auth::user()->id);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->save();

        // Here we will upload image
                
       
        
        if ($request->hasFile('image')) { 

            // Delete old image here
            File::delete(public_path('uploads/profile/'.$user->image));
            File::delete(public_path('uploads/profile/thumb/'.$user->image));

            $image = $request->file('image');

            $ext = strtolower($image->getClientOriginalExtension());
            $imageName = time() . '.' . $ext;

            $uploadPath = public_path('uploads/profile');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Save the original uploaded image
            $image->move($uploadPath, $imageName);

            $user->image = $imageName;
            $user->save();

            // Resize using Intervention Image v3 and GD
            $manager = new ImageManager(new Driver());

            $img = $manager->read($uploadPath . '/' . $imageName);
            $img->cover(150, 150);

            $thumbPath = $uploadPath . '/thumb';

            if (!file_exists($thumbPath)) {
                mkdir($thumbPath, 0755, true);
            }

            $img->save($thumbPath . '/' . $imageName);
        }

        return redirect()->route('account.profile')->with('success', 'Profile Updated Successfully');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }
}
