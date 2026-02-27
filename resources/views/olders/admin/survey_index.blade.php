@extends(backpack_view('blank'))

@section('content')
    <h1>Survey Dashboard</h1>
    <div>
        <ul>
            @foreach ($surveys as $survey)
                <li>
                    <a href="{{ url('/ngn-admin/survey_responses/' . $survey->id) }}">{{ $survey->title }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div id="responsesContainer" style="display: none;">
        <button onclick="goBack()">Back</button>
        <div id="responsesAccordion"></div>
    </div>
@endsection

@push('after_scripts')
    <script>
        function fetchResponses(surveyId) {
            fetch(`/survey-responses/${surveyId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('responsesAccordion');
                    container.innerHTML = '';
                    data.forEach(response => {
                        const card = document.createElement('div');
                        card.classList.add('card');
                        card.innerHTML = `
                            <div class="card-header" id="heading${response.id}">
                                <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" onclick="toggleCollapse('${response.id}')" aria-expanded="false" aria-controls="collapse${response.id}">
                                        👤 ${response.contact_name} | 📧 ${response.contact_email} | 📱 ${response.contact_phone} | 🕒 Submitted: ${new Date(response.created_at).toLocaleDateString()}
                                    </button>
                                </h2>
                            </div>
                            <div id="collapse${response.id}" class="collapse" aria-labelledby="heading${response.id}" style="display: none;">
                                <div class="card-body">
                                    <p>[Customer] [Opted in ${response.is_contact_opt_in ? '✅' : '❌'}]</p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Question</th>
                                                <th>Answer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${response.answers.map((answer, index) => `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${answer.question.question_text}</td>
                                                    <td>${answer.option ? answer.option.option_text : answer.answer_text}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                    document.getElementById('responsesContainer').style.display = 'block';
                })
                .catch(error => console.error('Error fetching responses:', error));
        }

        function toggleCollapse(responseId) {
            const element = document.getElementById('collapse' + responseId);
            element.style.display = element.style.display === 'none' ? 'block' : 'none';
        }

        function goBack() {
            document.getElementById('responsesContainer').style.display = 'none';
        }
    </script>
@endpush