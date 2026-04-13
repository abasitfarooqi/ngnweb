<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

@php
    $rating = $rating ?? 0; // Ensure $rating is defined and set to 0 if not passed
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;
@endphp

<div class="star-rating">
    @for ($i = 0; $i < $fullStars; $i++)
        <i class="fa fa-star" style="color: gold; font-size: 24px;"></i>
    @endfor
    @if ($halfStar)
        <i class="fa fa-star-half-o" style="color: rgb(138, 121, 21); font-size: 24px;"></i>
    @endif
    @for ($i = 0; $i < $emptyStars; $i++)
        <i class="fa fa-star-o" style="color: gold; font-size: 24px;"></i>
    @endfor
</div>
