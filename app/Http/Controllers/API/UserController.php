<?php

namespace App\Http\Controllers\API;

use http\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|max:191',
            'password' => 'required|string|min:8'
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = User::findorFail($id);
        $user->update($request->all());

        return['Message' => 'Updated User Info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $user = User::findorFail($id);

        $user->delete();

        return ["message" => "User deleted"];
    }

    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request) {
        $user = auth('api')->user();
        $currentPhoto = $user->photo;

        if ($request->photo != $currentPhoto) {
            $image_file_name = time().'.'.explode('/',explode(':', substr($request->photo,0,
                    strpos($request->photo,';')))[1])[1];

            $img = Image::make($request->photo);

            $img->save(public_path('image/profile/').$image_file_name);

            $request->merge(['photo' => $image_file_name]);

            //Delete the current photo
            $userPhoto = public_path('img/profile/').$currentPhoto;
            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }

        }


        $user->update($request->all());

        return ['message' => 'success'];
    }
}
