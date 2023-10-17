<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Photos;
use App\Models\Blog;
use App\Models\Like;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{   

    public function index()
    {
        $blogs = DB::table('blogs')
             ->join('likes', 'blogs.id', '=', 'likes.user_id')
             ->join('photos', 'blogs.id', '=', 'photos.imageable_id')
             ->select('title','description','url', 'user_id','likeable_id', 'likeable_type')
             ->orderBy('blogs.created_at', 'desc')
           ->paginate(5);
        return response()->json($blogs, 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('token-name')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function createblog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $path = $request->file('image')->store('images');

        $blog = Blog::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $blog->photos()->create([
            'url' => $path,
        ]);

        return response()->json(['message' => 'Blog Created successfully'], 201);
    }

    public function like(Request $request)
    {   
        $like = Like::firstOrNew([
            'user_id' => '1',
            'likeable_id' => $request->blog,
            'likeable_type' => 'App\Models\Blog',
        ]);

        if ($like->exists) {
            $like->delete();
            $message = 'Blog unliked successfully';
        } else {
            $like->user_id = '1';
            $like->likeable_id = $request->blog;
            $like->likeable_type = 'App\Models\Blog';
            $like->save();
            $message = 'Blog liked successfully';
        }

        return response()->json(['message' => $message], 200);
    }
}
