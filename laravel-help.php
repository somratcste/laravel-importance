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

@if(Session::has('success'))
	<div class="alert alert-success">
		{{Session::get('success')}}
	</div>
@endif

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
<?php
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

?>
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