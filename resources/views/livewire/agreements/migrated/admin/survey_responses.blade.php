@extends(backpack_view('blank'))

@section('content')

<style>
    body {
        font-size: 1.2em;
    }
    @@media screen and (max-width: 768px) {
        body {
            font-size: 1em;
        }
    }
    @@media screen and (max-width: 480px) {
        body {
            font-size: 0.8em;
        }
    }
    .summary {
        padding: 10px;
    }
    .summary-content {
        line-height: 1.5;
        background-color: #f0f0f0;
        margin-top: 10px;
        padding: 8px;
        margin-bottom: 5px;
        border-left: 5px solid #31708f;
    }
    .response-count, .opt-in-count, .favourite-choice {
        /* background:green; */
        padding:5px;
        /* color: antiquewhite; */
        border-radius:4px;
        
    }
    .opt-out-count {
        background:red;
        padding:8px;
        color: antiquewhite;
        border-radius:4px;
    }
    .question-text {
        padding:5px;
        /* background:#31708f; */
        color: #000;
        border-radius:4px;
        /* margin-right: 10px; */
    }
    .search-filter {
        padding: 10px;
    }
    #searchInput {
        min-width: 300px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #31708f;
    }
    .accordion {
        padding: 10px;
        max-height: 500px;
        overflow-y: scroll;
    }
    .responseCard {
        border-top: 1px solid #31708f;
        border-radius: 5px;
    }
    .card-header {
        cursor: pointer;
    }
</style>
    <!-- Summary -->
    <div class="summary">
        <h2>Survey Summary</h2>
        <p class="summary-content">
            <strong>Total Responses:</strong> <span class="response-count">{{ $responses->count() }}</span>
            | <strong>Opted In/Out:</strong> <span class="opt-in-count">{{ $responses->where('is_contact_opt_in', 1)->count() }}</span>
            <span class="opt-out-count">{{ $responses->where('is_contact_opt_in', 0)->count() }}</span>
        </p>
        @foreach ($responses->flatMap->answers->groupBy('question_id') as $questionId => $answers)
            @php
                $mostFrequentOption = $answers->pluck('option_id')->countBy()->sortDesc()->keys()->first();
                $mostFrequentOptionCount = $answers->pluck('option_id')->countBy()->sortDesc()->first();
                $questionText = $answers->first()->question->question_text;
                $mostFrequentOptionText = $mostFrequentOption ? \App\Models\NgnSurveyOption::find($mostFrequentOption)->option_text : 'No options selected';
            @endphp
            <div class="summary-content">
                <p ><strong>Question:</strong> <span class="question-text">
                    {{ $questionText }}</span></p>
           
                <p><strong>Top Choice:</strong><span class="favourite-choice">
                    {{ $mostFrequentOptionText }} (Selected {{ $mostFrequentOptionCount }} times)</span></p>
            </div>
        @endforeach
    </div>


    <!-- Search and Filter -->
    <div class="search-filter">
        <h3>Survey Responses</h3>
        <input type="text" id="searchInput" onkeyup="searchFunction()" placeholder="Search by name, phone, or email..">
        <label for="optInFilter">Filter by Opt In:</label>
        <select id="optInFilter" onchange="filterFunction()">
            <option value="">All</option>
            <option value="Yes">Opted In</option>
            <option value="No">Opted Out</option>
        </select>
    </div>


    <!-- Accordion -->
    <div class="accordion" id="responsesAccordion">



        @foreach ($responses as $response)
            <div class="card responseCard" data-name="{{ $response->contact_name }}"
                data-phone="{{ $response->contact_phone }}" data-email="{{ $response->contact_email }}"
                data-optin="{{ $response->is_contact_opt_in ? 'Yes' : 'No' }}">
                <div class="card-header" id="heading{{ $response->id }}" onclick="toggleCollapse('{{ $response->id }}')">
                    <p class="mb-0">
                        <i id="icon{{ $response->id }}" class="la la-plus-circle"></i>
                         Name: {{ $response->contact_name }} - Email: {{ $response->contact_email }} - Phone: {{ $response->contact_phone }} - Submitted: {{ $response->created_at->format('d M Y') }} - Opted in: {{ $response->is_contact_opt_in ? 'Yes' : 'No' }}
                    </p>
                </div>

                <div id="collapse{{ $response->id }}" class="collapse" aria-labelledby="heading{{ $response->id }}">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Answer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($response->answers as $index => $answer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $answer->question->question_text }}</td>
                                        <td>{{ $answer->option ? $answer->option->option_text : $answer->answer_text }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@push('after_scripts')
    <script>
        function toggleCollapse(responseId) {
            var element = document.getElementById('collapse' + responseId);
            var icon = document.getElementById('icon' + responseId);
            if (element.style.display === 'none') {
                element.style.display = 'block';
                icon.className = "la la-minus-circle";
            } else {
                element.style.display = 'none';
                icon.className = "la la-plus-circle";
            }
        }

        function searchFunction() {
            var input, filter, cards, cardContainer, name, phone, email, i;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            cardContainer = document.getElementById("responsesAccordion");
            cards = cardContainer.getElementsByClassName("responseCard");
            for (i = 0; i < cards.length; i++) {
                name = cards[i].getAttribute("data-name");
                phone = cards[i].getAttribute("data-phone");
                email = cards[i].getAttribute("data-email");
                if (name.toUpperCase().indexOf(filter) > -1 || phone.toUpperCase().indexOf(filter) > -1 || email.toUpperCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        function filterFunction() {
            var select, filter, cards, cardContainer, optIn, i;
            select = document.getElementById("optInFilter");
            filter = select.value;
            cardContainer = document.getElementById("responsesAccordion");
            cards = cardContainer.getElementsByClassName("responseCard");
            for (i = 0; i < cards.length; i++) {
                optIn = cards[i].getAttribute("data-optin");
                if (filter === "" || optIn === filter) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
    </script>
@endpush
