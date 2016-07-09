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