<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Category;
use App\Post;
use App\Role;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $posts = new Post();
        $posts = $posts->get();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $roles = Role::all();
        $categories = Category::all();
        return view('posts.create', compact('roles', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'role_ids' => 'required|array',
            'title' => 'required|min:3',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'description' => 'required|min:3',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'date' => 'required|date',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $this->__main_control_block($data);
        return redirect(route('posts.index'))->with('message', 'Post successfully created');
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $roles = Role::all();
        $categories = Category::all();
        return view('posts.edit', compact('roles', 'categories', 'post'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'role_ids' => 'required|array',
            'title' => 'required|min:3',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'description' => 'required|min:3',
            'date' => 'required|date',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $this->__main_control_block($data, $id);
        return redirect(route('posts.index'))->with('message', 'Post successfully updated');
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $post->favorites()->detach($post->favorites()->pluck('id'));
        $post->roles()->detach($post->roles()->pluck('id'));
        $post->delete();
        return redirect(route('posts.index'))->with('message', 'Post successfully removed');
    }

    private function __main_control_block($data, $id = null)
    {
        if (!$post = Post::find($id)) {
            $post = new Post();
        }
        if (isset($data['file'])) {
            if ($post->image) {
                if (file_exists('/storage/posts/' . $post->image)) {
                    unlink('/storage/posts/' . $post->image);
                }
            }
            $fileName = $this->__files_control_block($data['file'], null);
            $post->image = $fileName;
        }
        $post->title = $data['title'];
        $post->description = $data['description'];
        $post->date = $data['date'];
        $post->video_url = $data['video_url'];
        $post->category_id = $data['category_id'];
        $post->subcategory_id = $data['subcategory_id'];
        $post->save();
        $post->roles()->sync($data['role_ids']);
        return $post;
    }

    private function __files_control_block($file)
    {
        $fileName = time() . '.' . $file->extension();
        $file->move(storage_path('app/public/posts/'), $fileName);
        return $fileName;
    }
}
