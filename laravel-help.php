Download or install Laravel :
================================

composer create-project --prefer-dist laravel/laravel blog

================================

Pagination : 
=================================
<section>
	<nav>
		<ul class="pager">
		  	@if($posts->currentPage() !== 1)
		    	<li class="previous"><a href="{{ $posts->previousPageUrl() }}"><span aria-hidden="true">&larr;</span> Older</a></li>
		    @endif
		    @if($posts->currentPage() !== $posts->lastPage() && $posts->hasPages())
		    	<li class="next"><a href="{{ $posts->nextPageUrl() }}">Newer <span aria-hidden="true">&rarr;</span></a></li>
		    @endif
		</ul>
	</nav>
</section>

===================================

Validation:
===================================
@if(count($errors) > 0)
	<div>
		<ul class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{$error}}
			@endforeach
		</ul>
	</div>
@endif

<?php 
public function postQuote(Request $request)
{
	$this->validate($request , [
		'author' => 'required|max:60|alpha',
		'quote'	 => 'required|max:500'
	]);
	$authorText = ucfirst($request['author']);
	$quoteText 	= $request['quote'];

	$author = Author::where('name' , $authorText)->first();
	if(!$author)
	{
		$author = new Author();
		$author->name = $authorText;
		$author->save();
	}
	$quote = new Quote();
	$quote->quote = $quoteText;
	$author->quotes()->save($quote);

	return redirect()->route('home')->with([
		'success' => 'Quote Saved.'
	]);
}
?>

Segment :
=======================================
<?php
@if(Session::has('success'))
	<div class="alert alert-success">
		{{Session::get('success')}}
	</div>
@endif

@if(!empty(Request::Segment(1)))
	<section class="filter-bar">
		A filter has been set ! <a href="{{ route('home') }}">Show All Quotes</a>
	</section>
@endif

?>
css and js connection and master blade:
========================================

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>@yield('title')</title>
		<link rel="stylesheet" type="text/css" href="{{ asset('src/css/bootstrap.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('src/css/main.css') }}">
	</head>
	<body>
		<div class="container">
			@include('includes.header')
			<div class="main">
				@yield('content')
			</div>
		</div>
	</body>
	@include('includes.footer')
</html>

======================================

Route group :
=====================================
<?php
Route::group(['middleware' => ['web']] , function() {

	Route::get('/{author?}' , [
	'uses' => 'HomeController@getHome',
	'as' => 'home'
	]);

	Route::post('/new' , [
	'uses' => 'QuoteController@postQuote',
	'as' => 'create'
	]);

	Route::get('/delete/{quote_id}' , [
	'uses' => 'QuoteController@getDeleteQuote',
	'as' => 'delete'
	]);
});
?>
=======================================

Route : 
======================================
<?php 
Route::get('/admin' , function() {
        return view('admin.index');
    })->name('admin');

?>
======================================

Route prefix : 
======================================
<?php
Route::group(['prefix' => '/admin'] , function() {
        
    Route::get('/' , [
        'uses' => 'AdminController@getIndex',
        'as' => 'admin.index'
    ]);
});
?>

Controller :
=====================================
<?php

namespace App\Http\Controllers;


class AdminController extends Controller 
{
	public function getLogin()
	{
		return view('admin.login');
	}
}
?>
====================================

Request Facade :
====================================
<?php
use Illuminate\Http\Request;
?>
====================================

form :
====================================

<form method="post" action="{{ route('admin.login') }}">
	<div class="input-group">
		<label for="name">Your Name : </label>
		<input type="text" name="name" id="name" placeholder="Your name">
	</div>
	<div class="input-group">
		<label for="password">Your E-Mail : </label>
		<input type="password" name="password" id="password" placeholder="Your E-Mail">
	</div>
	
	<button type="submit" class="btn btn-primary">Submti</button>
	<input type="hidden" name="_token" value="{{Session::token()}}">
</form>


=======================================

Auth Facade :
=======================================
<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Authenticatable;
?>

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;

class Admin extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use Authenticatable;
}

?>
======================================

Session and with message : 
=====================================

<?php
@if(session('fail'))
	<div class="alert alert-danger">
		{{session('fail')}}
	</div>
@endif

return redirect('admin/login')->with('fail' , 'Could not be login');

?>

=====================================

div center :
=====================================
.center {
    margin: auto;
    width: 50%;
    border: 3px solid green;
    padding: 10px;
}

=====================================

Read More : 
=====================================
<?php

public function getBlogIndex()
	{
		$posts = Post::orderBy('created_at' , 'desc')->paginate(5);
		foreach ($posts as $post ) {
			$post->body = $this->shortenText($post->body , 50);
		}
		return view('frontend.blog.index' , ['posts' => $posts]);
	}

public function shortenText($text , $words_count)
	{
		if(str_word_count($text , 0) > $words_count)
		{
			$words = str_word_count($text , 2);
			$pos = array_keys($words);
			$text = substr($text , 0 , $pos[$words_count]) . '.....' ;
		}
		return $text;
	}

?>

=======================================

Edit post value : 
=======================================
<?php 
value="{{ Request::old('title') ? Request::old('title') : isset($post) ? $post->title : '' }} "
?>

========================================

if not post controller function : 
========================================
<?php
if(!$post){
			return redirect()->route('blog.index')->with(['fail' => 'Page not found !']);
		}
?>

=======================================

Active Navigation Menu : 
=======================================
<?php 
{{ Request::is('admin/posts') ? 'class=active' : ''}}
?>
=======================================

Using Modal : 
=======================================
<a data-toggle="modal" data-target="#editCategory<?php echo $i ; ?>" href="">Edit</a> | 
</div>
<!-- Modal -->
<div class="modal fade" id="editCategory<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Category</h4>
      </div>
      <div class="modal-body">
       		<input type="text" class="form-control" id="exampleInputName2" name="name" id="name" value="{{ $category->name }}">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

============================================

Update Post Controller function : 
============================================
<?php 
public function postUpdatePost(Request $request) 
	{
		$this->validate($request , [
			'title' => 'required|max:120' ,
			'author' => 'required|max:80' ,
			'body' => 'required'
		]);

		$post = Post::find($request['post_id']);
		$post->title = $request['title'];
		$post->author = $request['author'];
		$post->body = $request['body'];
		$post->update();

		//categories
		
		return redirect()->route('admin.index')->with(['success' => 'Post Updated Successfully ! ']);

	}
?>

<input type="hidden" name="_token" value="{{Session::token()}}">
<input type="hidden" name="category_id" value="{{ $category->id}}">

==========================================

Delete Post Controller function Using modal  : 
=========================================
<?php 
public function getDeleteCategory(Request $request)
	{
		$category = Category::find($request['category_id']);
		if(!$category){
			return redirect()->route('blog.index')->with(['fail' => 'Page not found !']);
		}
		$category->delete();
		return redirect()->route('admin.blog.categories')->with(['success' => 'Category Deleted Successfully !']);

	}

?>

<input type="hidden" name="category_id" value="{{ $category->id}}">

=========================================

Delete post withoug modal : 
=========================================
<a class="delete" href="{{ route('admin.blog.post.delete' , ['post_id' => $post->id ]) }}">Delete</a>

<?php 

Route::get('/blog/post/{post_id}/delete' , [
    'uses' => 'PostController@getDeletePost',
    'as' => 'admin.blog.post.delete'
]);

public function getDeletePost($post_id)
	{
		$post = Post::find($post_id);
		if(!$post){
			return redirect()->route('blog.index')->with(['fail' => 'Page not found !']);
		}
		$post->delete();
		return redirect()->route('admin.index')->with(['success' => 'Post Deleted Successfully !']);

	}

?>

============================================

Join and Distinct : 
===========================================
<?php 
$categories = DB::table('categories')
                    ->join('posts' , 'categories.id' , '=' , 'posts.category_id')
                    ->select('categories.name')
                    ->distinct()
                    ->get();
?>
============================================

Mobile Number Validation : 
===========================================
<div class="form-group">
  <label class="col-sm-2 control-label">Mobile Number</label> 
  <div class="col-sm-8">
    <input class="form-control" type="tel" pattern="^\d{11}$" required name="mobile" placeholder="(format: xxxxxxxxxxx)" name="mobileNumber">
  </div>
</div>

=========================================

upload file / image : 
=========================================
<?php 
if(Input::hasFile('image')){
    $file = Input::file('image');
    $file->move(public_path(). '/',$file->getClientOriginalName());

    $doctor->image = $file->getClientOriginalName();
    $doctor->size = $file->getClientsize();
    $doctor->type = $file->getClientMimeType();
}
?>

update file / image : 
==========================================
<?php 
if(Input::hasFile('image')){
    File::delete('public/'.$doctor->image);
    $file = Input::file('image');
    $file->move(public_path(). '/',$file->getClientOriginalName());

    $doctor->image = $file->getClientOriginalName();
    $doctor->size = $file->getClientsize();
    $doctor->type = $file->getClientMimeType();
}
?>

nice modal : 
===========================================
<td><a data-toggle="modal" data-target="#details<?php echo $i; ?>" href=""><button type="button" class="btn btn-success">Details</button></a></td>
<div class="modal" id="details<?php echo $i; ?>" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h4 class="modal-title">Contact</h4>
    </div>
    <div class="modal-body">
      <p>Feel free to contact us for any issues you might have with our products.</p>
      <div class="row mb25">
        <div class="col-xs-6">
          <label>Name</label>
          <input type="text" class="form-control" placeholder="Name">
        </div>
        <div class="col-xs-6">
          <label>Email</label>
          <input type="text" class="form-control" placeholder="Email">
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <label>Message</label>
          <textarea class="form-control" rows="3"></textarea>
        </div>
      </div>
    </div>
    <div class="modal-footer no-border">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary">Send</button>
    </div>
  </div>
</div>
</div>
====================================

checked or selected : 
====================================
<?php 
@foreach ($categories as $category)
	<option value="{{ $category->id}}" {{ $post->category_id == $category->id ? 'selected'  : '' }}> {{ $category->name }}</option>
@endforeach
?>
====================================