@if($resolvedView !== '' && view()->exists($resolvedView))
    @include($resolvedView)
@endif
