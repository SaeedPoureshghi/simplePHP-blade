<ul class="breadcrumb">
    <li><a href="/">{{lang('home')}}</a></li>
    @foreach(Route::getBreadcrumbs() as $key => $segment)
        @if($key + 1 == count(Route::getBreadcrumbs()))
            <li>{{ lang(ucfirst($segment)) }}</li> <!-- Last segment (current page) -->
        @else
            <li><a href="{{ '/' . implode('/', array_slice(Route::getBreadcrumbs(), 0, $key + 1)) }}">{{ lang(ucfirst($segment)) }}</a></li>
        @endif
    @endforeach
</ul>
