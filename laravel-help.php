Pagination : 
=================================
<div class="pagination1">
	@if($quotes->currentPage() !== 1)
		<a href="{{ $quotes->previousPageUrl() }}"><span class="fa fa-caret-left"><</span></a>
	@endif
	@if($quotes->currentPage() !== $quotes->lastPage() && $quotes->hasPages())
		<a href="{{ $quotes->nextPageUrl() }}"><span class="fa fa-caret-right">></span></a>
	@endif
</div>

.pagination1 {
	font-size: 20px;
}

.pagination1 a {
	color :black;
	text-decoration: none;
}

.pagination1 a:hover , 
.pagination1 a:active {
	color: #ccc;
}

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
	
	<button type="submit" class="btn btn-primary">Submti Quote</button>
	<input type="hidden" name="_token" value="{{Session::token()}}">
</form>

?>
=======================================