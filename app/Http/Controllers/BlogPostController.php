<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Http\Resources\BlogsResourc;
use App\Http\Resources\PostsResource;
use App\Post;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    protected $user;
    protected $limit = 20;

    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function getBlogs(Request $request)
    {
    
        $data = $request->all();
        $blogs = new Blog();
        if (isset($data['category_id'])) {
            $blogs = $blogs->where('category_id', $data['category_id']);
        }

        if (isset($data['search'])) {
            
            $blogs = $blogs->where('title', 'LIKE', '%' . $data['search'] . '%');
        }
        if ($this->user) {
            $blogs = $blogs->whereHas('roles', function ($query) {
                $query->where('role_id', $this->user->role_id);
            });
        }
        

        $blogs = $blogs->get();
        if (!empty($blogs)) {
          return BlogsResourc::collection($blogs);
        }else{
            return response()->json(['message' => 'nothing was found'], 200);
        }

    }

    public function getPosts(Request $request)
    {
        $data = $request->all();

        $post = new Post();
        if (isset($data['category_id'])) {
            $post = $post->where('category_id', $data['category_id']);
        }
        if (isset($data['search'])) {
            $post = $post->where('title', 'LIKE', '%' . $data['search'] . '%');
        }
        if ($this->user) {
            $post = $post->whereHas('roles', function ($query) {
                $query->where('role_id', $this->user->role_id);
            });
        }
        $posts = $post->get();
        return PostsResource::collection($posts);
    }

    public function showPost($id)
    {

        if (!$post = Post::find($id)) {
            return response()->json(['message' => 'post not found'], 404);
        }

        return new PostsResource($post);

    }

    public function postFavorite($id)
    {

        if($this->user){
            $post = Post::find($id);
            if (!$post) {
                return response()->json(['message' => 'Not found this post'], 404);
            }
            if ($this->user->favorite_posts()->where('post_id', $id)->first()) {
                $this->user->favorite_posts()->detach($id);
            } else {
                $this->user->favorite_posts()->attach($id);
            }
            return response()->json(['message' => true], 200);
        }else{
            return response()->json(['status' => 'Authorization Token not found'], 401);
        }

    }
}
