@if ($rating)
    @for ($i = 1; $i<= 5; $i++ )
        @if ( $i <= round($rating))
            <span class="text-amber-500">★</span>
        @else
            <span class="text-slate-700">☆</span> 
        @endif
    @endfor
@else
    No Rating yet !
@endif