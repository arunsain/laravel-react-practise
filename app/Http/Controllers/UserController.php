<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return Inertia::render('UserList');
        //$user = User::all();
        if(isset($request->name) && $request->name != ""){
               $user = User::where('name','LIKE','%'.$request->name.'%')->paginate(10);
               $user->appends($request->all());
        }else{
                   $user = User::paginate(10);
        }
     

        //print_r($user);
        return Inertia::render('UserList',[ 'users' => $user ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return Inertia::render('FormSubmit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required',
           
        ]);

         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
         return redirect('/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $user =   User::find($id);
    //     print_r($user);
    //     die('hello');
    return Inertia::render('EditUser',['user' =>$user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
			'name' =>['required','string'],
			'email' => ['required', 'string', 'email', 'max:255','unique:users,email,'.$id],
		]);


		$user = User::find($id);
		$user->name = $request->name;
		$user->email = $request->email;
        if($request->password != ""){
            $user->password =  Hash::make($request->password);
        }
	
		$user->save();
		  return redirect('/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user =   User::find($id);
        $user->delete();
        return redirect('users');
    }


    public function addImage($id)
    {
       return Inertia::render('AddImage',['userId' =>$id]);
    }

     public function uploadImage(Request $request)
    {
    $id = $request->userId;
      $name = $request->file('avatar')->getClientOriginalName();

        $path = $request->file('avatar')->store('public/images');
       $path =  substr($path,6);
        	$user = User::find($id);
		$user->profile_image = $path;
        $user->save();
       
            
         return redirect('/user');
    }
}