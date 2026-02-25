  <!--
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="">
                    <div class="col-12 mb-4">
                        <h3 class="">What Our Customers Say</h3>
                        <p class="lead">Hear from our satisfied customers on Trustpilot.</p>
                    </div>

                    
                    <div id="reviewsCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">
                            
                            @if (!empty($reviews['reviews']))
                                @foreach (array_chunk($reviews['reviews'], 2) as $index => $reviewChunk)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between">
                                            @foreach ($reviewChunk as $review)
                                                <div class="col-12 review-card">
                                                    <div class="d-flex mb-3">
                                                        <div class="avatar mr-3" style="width: 49px; height: 49px;">
                                                            {{ substr($review['consumer']['name'], 0, 2) }}</div>
                                                        <div style="margin-left: 10px;">
                                                            <h5 class="mb-0">{{ $review['consumer']['name'] }}</h5>
                                                            <span class="date">{{ \Carbon\Carbon::parse($review['created_at'])->format('F d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="star-rating mb-2">
                                                        <img loading="lazy" src="https://cdn.trustpilot.net/brand-assets/4.1.0/stars/stars-{{ $review['stars'] }}.svg"
                                                            alt="Rated {{ $review['stars'] }} out of 5 stars">
                                                    </div>
                                                    <h3 class="review-title mb-2">{{ $review['title'] }}</h3>
                                                    <p class="mb-0 review-para">{{ $review['text'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                
                                <div class="carousel-item active">
                                    <div class="d-flex justify-content-between">
                                        <div class="col-12 review-card">
                                            <div class="d-flex mb-3">
                                                <div class="avatar mr-3" style="width: 49px; height: 49px;">TS</div>
                                                <div style="margin-left: 10px;">
                                                    <h5 class="mb-0">Tahir SHAKOOR</h5>
                                                    <span class="date">April 03, 2024</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="star-rating mb-2">
                                                <img loading="lazy" src="https://cdn.trustpilot.net/brand-assets/4.1.0/stars/stars-5.svg"
                                                    alt="Rated 5 out of 5 stars">
                                            </div>
                                            <h3 class="review-title mb-2">Nequinho motors ltd sets the standard for motorcycle service...</h3>
                                            <p class="mb-0 review-para">Nequinho motors ltd sets the standard for motorcycle service. With a knowledgeable sales team and skilled mechanics, they provide top-notch support for riders. Trustworthy and reliable, they've earned my loyalty. Highly recommended!</p>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="carousel-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="col-12 review-card">
                                            <div class="d-flex mb-3">
                                                <div class="avatar mr-3" style="width: 49px; height: 49px;">TS</div>
                                                <div style="margin-left: 10px;">
                                                    <h5 class="mb-0">Aqib Raja</h5>
                                                    <span class="date">April 01, 2024</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="star-rating mb-2">
                                                <img loading="lazy" src="https://cdn.trustpilot.net/brand-assets/4.1.0/stars/stars-5.svg"
                                                    alt="Rated 5 out of 5 stars">
                                            </div>
                                            <h3 class="review-title mb-2">Good expressions, professional staff...</h3>
                                            <p class="mb-0 review-para">The sales team is knowledgeable and friendly, helping me find the perfect bike. The mechanics are skilled and reliable, ensuring my motorcycle runs smoothly. Overall, a top-notch destination for any rider's needs. Highly recommend!</p>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="carousel-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="col-12 review-card">
                                            <div class="d-flex mb-3">
                                                <div class="avatar mr-3" style="width: 49px; height: 49px;">R</div>
                                                <div style="margin-left: 10px;">
                                                    <h5 class="mb-0">Ramla Ibrahim</h5>
                                                    <span class="date">April 05, 2024</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="star-rating mb-2">
                                                <img loading="lazy" src="https://cdn.trustpilot.net/brand-assets/4.1.0/stars/stars-5.svg"
                                                    alt="Rated 5 out of 5 stars">
                                            </div>
                                            <h3 class="review-title mb-2">Very professional and friendly staff...</h3>
                                            <p class="mb-0 review-para">The guys were a massive help in buying my first motorbike. They were very professional, super helpful in explaining the process and were very patient with me. I highly recommend, I’ve had an amazing first-time buyer experience. Thank you guys.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                
            </div>
-->