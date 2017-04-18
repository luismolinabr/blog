<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests\CommentRequest;

class CommentsController extends Controller
{
    public function store(CommentRequest $request, Post $post)
    {
    	$post->addComment(request('body'));

		return back();
    }
    
}
