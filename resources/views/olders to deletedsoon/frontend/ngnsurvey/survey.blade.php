@extends('olders.frontend.main_master_noheadfoot')

@section('title', $survey->title . ' - Surveys - NGN - Motorcycle Rentals, Repairs in UK')

@section('content')

<style>
                            @media screen and (max-width: 600px) {
                                .breadcrumbul-parallax {
                                    font-size: 14px !important;
                                }
                            }
                            @media screen and (min-width: 601px) {
                                .breadcrumbul-parallax {
                                    font-size: 16px;
                                }
                            }
                            h4{
                                margin-top: 20px !important;
                            }
                            
                            .logo-containerx{
                                background-color: rgba(0, 0, 0, 0.5);
                                padding: 10px;
                                border-radius: 5px;
                                margin:0 auto;
                                max-width: 170px;
                            }

                            /* Modal Styles */
                            .modal-backdrop {
                                background-color: rgba(0, 0, 0, 0.5);
                                z-index: 1040;
                            }

                            .modal {
                                z-index: 1050;
                            }

                            .modal-content {
                                background-color: #fff;
                                border-radius: 8px;
                                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                            }

                            .modal-header {
                                border-bottom: 1px solid #eee;
                                padding: 15px 20px;
                            }

                            .modal-body {
                                padding: 20px;
                                text-align: center;
                            }

                            .modal-title {
                                color: #333;
                                font-weight: 600;
                            }
                        </style>
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <div class="logo-containerx">
                        <img loading="lazy" src="/img/ngn-motor-logo-fit-small.png" alt="NGN Motor" style="height: 58px; width: auto;">
                    </div>
                    <ul class="breadcrumbul-parallax" style="font-weight: 800;">
                       
                        <li><a href="/">Home Page</a></li>
                        <li>Survey</li>
                        <li>{{ $survey->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <h2 class="text-center mb-3">{{ $survey->title }}</h2>
        <form id="survey-form" action="{{ route('survey.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <div class="survey-description" style="background:#ececec;padding:15px;">
                <p>{!! $survey->description !!}</p>
            </div>

            <h3 class="mt-4 active-color">Survey Questions</h3>
            <div id="questions-container">
                @foreach($survey->questions as $question)
                    <div class="form-group mt-2">
                        <label for="question_{{ $question->id }}" style="font-weight: 700;">{{ $question->question_text }}</label>
                        @if($question->question_type === 'single_choice')
                            @foreach($question->options as $option)
                                <div>
                                    <input type="radio" name="answers[{{ $question->id }}][option_id]" value="{{ $option->id }}"
                                        id="option_{{ $option->id }}" required>
                                    <label for="option_{{ $option->id }}" style="font-weight: 400;">{{ $option->option_text }}</label>
                                </div>
                            @endforeach
                        @elseif($question->question_type === 'multiple_choice')
                            @foreach($question->options as $option)
                                <div>
                                    <input type="checkbox" name="answers[{{ $question->id }}][option_id][]" value="{{ $option->id }}"
                                        id="option_{{ $option->id }}">
                                    <label for="option_{{ $option->id }}" style="font-weight: 400;">{{ $option->option_text }}</label>
                                </div>
                            @endforeach
                        @else
                            <input type="text" name="answers[{{ $question->id }}][answer_text]" class="form-control"
                                placeholder="Enter your answer" required>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-4 active-color">
                <h3>Contact Information</h3>
                <div class="row">
                    <div class="form-group col-lg-4 col-md-12">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group col-lg-4 col-md-12">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email"
                            required>
                    </div>
                    <div class="form-group col-lg-4 col-md-12">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number">
                    </div>
                </div>
                <div class="form-group form-check">
                    <input type="hidden" name="opt_in" value="0">
                    <input type="checkbox" name="opt_in" id="opt_in" class="form-check-input" value="1" q>
                    <label for="opt_in" class="form-check-label">I agree, to be contacted with offers and information</label>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="ngn-btn ngn-btn-primary w-100 mt-2 bg-black">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Thank You Modal -->
    <div class="modal fade" id="thankYouModal" tabindex="-1" role="dialog" aria-labelledby="thankYouModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-center" id="thankYouModalLabel">Thank You!</h5>
                </div>
                <div class="modal-body">
                    <p>Thank you for completing our survey. Your feedback is valuable to us!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="https://wa.me/?text={{ urlencode('NGN Motors - Motorbike Preference Survey: ' . route('survey.showBySlug', ['slug' => $survey->slug])) }}"
            target="_blank" class="btn ngn-btn ngn-bg ">
            Share on WhatsApp
        </a>
    </div>

    <script>
        $(document).ready(function() {
            $('#survey-form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#survey-form')[0].reset();
                        $('#thankYouModal').modal('show');
                        
                        // Automatically close the modal after 3 seconds
                        setTimeout(function() {
                            $('#thankYouModal').modal('hide');
                        }, 3000);
                    },
                    error: function(xhr) {
                        alert('An error occurred while submitting the survey. Please try again.');
                    }
                });
            });

            // Handle modal hidden event
            $('#thankYouModal').on('hidden.bs.modal', function () {
                // Reset form when modal is closed
                $('#survey-form')[0].reset();
            });
        });
    </script>
@endsection