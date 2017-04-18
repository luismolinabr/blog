<div class="col-sm-3 offset-sm-1 blog-sidebar">
  <div class="sidebar-module">
    <h4>Archives</h4>
    <ol class="list-unstyled">

      @foreach ($archives as $stats)
        <li>
          <a href="/?month={{ $stats['month'] }}&year={{ $stats['year'] }}" 
          id={{ $stats['month'] . '-' . $stats['year'] }}>
            {{ $stats['monthName'] . ' ' . $stats['year'] }}
          </a>
        </li>
      @endforeach

    </ol>
  </div>
</div>
