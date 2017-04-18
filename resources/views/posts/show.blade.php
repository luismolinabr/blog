@extends ('layouts.master')

@section ('content')
  <div class="col-sm-8 blog-main">
    <h2> {{ $post->title }} </h2>

    <p>
      por {{ $post->user->name}} em {{ $post->created_at->format('d/m/Y H:i') }}
      {{-- por {{ $post->user->name}} em {{ $post->created_at->formatLocalized('%d %B %Y'); }} --}}
    </p>

    {{ $post->body }}

    <hr>

    <div class="comments">
    	<ul class="list-group">
  	  	@foreach ($post->commentsLatest() as $comment)
  	  		<li class="list-group-item">
  		  		{{ $comment->body }} 
            <strong>
             &nbsp por {{ $comment->user->name }} em {{ $comment->created_at->diffForHumans() }}
            </strong>
  	  		</li>
  	  	@endforeach
    	</ul>
    </div>


    @if (Auth::check() && Auth::id() != $post->user->id)
      <hr>

      <div class="card">
        <div class="card-block">
          <form method="POST" action="/posts/{{ $post->id }}/comments">
            {{ csrf_field() }}

            @include ('layouts.errors')

            <div class="form-group">
              <textarea class="form-control" name="body" placeholder="Your comment here." required></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Add Comment</button>
            </div>
          </form>
        </div>
      </div>

    @endif

  </div>
@endsection