<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Category;
use App\Role;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    public function index()
    {
        $blogs = Blog::all();
        return view('blogs.index',compact('blogs'));
    }

    public function create()
    {
        $roles = Role::all();
        $categories = Category::all();
        return view('blogs.create', compact('roles', 'categories'));
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
        return redirect(route('blogs.index'))->with('message', 'Blog successfully created');
    }

    public function edit($id)
    {
        $blog = Blog::find($id);
        $roles = Role::all();
        $categories = Category::all();
        return view('blogs.edit', compact('roles', 'categories', 'blog'));
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
        return redirect(route('blogs.index'))->with('message', 'Blog successfully updated');
    }

    public function destroy($id)
    {
        $post = Blog::find($id);
        $post->roles()->detach($post->roles()->pluck('id'));
        $post->delete();
        return redirect(route('blogs.index'))->with('message', 'Blog successfully removed');
    }

    private function __main_control_block($data, $id = null)
    {
        if (!$post = Blog::find($id)) {
            $post = new Blog();
        }
        if (isset($data['file'])) {
            if ($post->image) {
                if (file_exists('/storage/blogs/' . $post->image)) {
                    unlink('/storage/blogs/' . $post->image);
                }
            }
            $fileName = $this->__files_control_block($data['file'], null);
            $post->image = $fileName;
        }
        $post->title = $data['title'];
        $post->description = $data['description'];
        $post->date = $data['date'];
        $post->category_id = $data['category_id'];
        $post->subcategory_id = $data['subcategory_id'];
        $post->save();
        $post->roles()->sync($data['role_ids']);
        return $post;
    }

    private function __files_control_block($file)
    {
        $fileName = time() . '.' . $file->extension();
        $file->move(storage_path('app/public/blogs/'), $fileName);
        return $fileName;
    }
}
