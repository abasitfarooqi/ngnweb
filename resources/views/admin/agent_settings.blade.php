@extends(backpack_view('blank'))

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <strong>Please fix the highlighted fields.</strong>
        </div>
    @endif

    <div class="mb-3">
        <h2 class="mb-1">AI Chat Agent Settings</h2>
        <div class="text-muted">
            Configure the DigitalOcean GenAI Agent endpoint used by the public chat widget.
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('page.agent_settings.update') }}">
                @csrf

                <div class="mb-3">
                    <label for="endpoint_url" class="form-label">Agent Endpoint URL</label>
                    <input
                        id="endpoint_url"
                        name="endpoint_url"
                        type="url"
                        class="form-control @error('endpoint_url') is-invalid @enderror"
                        value="{{ old('endpoint_url', $endpointUrl) }}"
                        required
                        placeholder="https://q3fdfxbbqy7r2fw5ezowvysq.agents.do-ai.run/your-agent-endpoint"
                    >
                    @error('endpoint_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <small class="form-text text-muted">
                            Enter the base URL (e.g. https://q3fdfxbbqy7r2fw5ezowvysq.agents.do-ai.run). The system will automatically append /api/v1/chat/completions.
                        </small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="access_key" class="form-label">Endpoint Access Key</label>
                    <input
                        id="access_key"
                        name="access_key"
                        type="password"
                        class="form-control @error('access_key') is-invalid @enderror"
                        value="{{ old('access_key', $accessKey) }}"
                        required
                        autocomplete="off"
                    >
                    @error('access_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <small class="form-text text-muted">
                            Use the Endpoint Access Key from the DigitalOcean dashboard. This is never exposed to visitors.
                        </small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="max_tokens" class="form-label">
                            Max Tokens: <span id="max_tokens_value" class="fw-bold">{{ old('max_tokens', $maxTokens) }}</span>
                        </label>
                        <input
                            type="range"
                            id="max_tokens_slider"
                            class="form-range @error('max_tokens') is-invalid @enderror"
                            min="128"
                            max="16384"
                            step="128"
                            value="{{ old('max_tokens', $maxTokens) }}"
                            oninput="document.getElementById('max_tokens_value').textContent = this.value; document.getElementById('max_tokens').value = this.value;"
                        >
                        <input
                            type="hidden"
                            id="max_tokens"
                            name="max_tokens"
                            value="{{ old('max_tokens', $maxTokens) }}"
                        >
                        @error('max_tokens')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <small class="form-text text-muted">Range: 128-16384. Default: 2001</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="temperature" class="form-label">
                            Temperature: <span id="temperature_value" class="fw-bold">{{ old('temperature', $temperature) }}</span>
                        </label>
                        <input
                            type="range"
                            id="temperature_slider"
                            class="form-range @error('temperature') is-invalid @enderror"
                            min="0"
                            max="100"
                            step="1"
                            value="{{ round((float)old('temperature', $temperature) * 100) }}"
                            oninput="var val = (this.value / 100).toFixed(2); document.getElementById('temperature_value').textContent = val; document.getElementById('temperature').value = val;"
                        >
                        <input
                            type="hidden"
                            id="temperature"
                            name="temperature"
                            value="{{ old('temperature', $temperature) }}"
                        >
                        @error('temperature')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <small class="form-text text-muted">Range: 0.0-1.0. Higher = more random. Default: 0.7</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="top_p" class="form-label">
                            Top P: <span id="top_p_value" class="fw-bold">{{ old('top_p', $topP) }}</span>
                        </label>
                        <input
                            type="range"
                            id="top_p_slider"
                            class="form-range @error('top_p') is-invalid @enderror"
                            min="10"
                            max="100"
                            step="1"
                            value="{{ round((float)old('top_p', $topP) * 100) }}"
                            oninput="var val = (this.value / 100).toFixed(2); document.getElementById('top_p_value').textContent = val; document.getElementById('top_p').value = val;"
                        >
                        <input
                            type="hidden"
                            id="top_p"
                            name="top_p"
                            value="{{ old('top_p', $topP) }}"
                        >
                        @error('top_p')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <small class="form-text text-muted">Range: 0.1-1.0. Nucleus sampling threshold. Default: 0.9</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="top_k" class="form-label">
                            Top K: <span id="top_k_value" class="fw-bold">{{ old('top_k', $topK) }}</span>
                        </label>
                        <input
                            type="range"
                            id="top_k_slider"
                            class="form-range @error('top_k') is-invalid @enderror"
                            min="0"
                            max="10"
                            step="1"
                            value="{{ old('top_k', $topK) }}"
                            oninput="document.getElementById('top_k_value').textContent = this.value; document.getElementById('top_k').value = this.value;"
                        >
                        <input
                            type="hidden"
                            id="top_k"
                            name="top_k"
                            value="{{ old('top_k', $topK) }}"
                        >
                        @error('top_k')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <small class="form-text text-muted">Range: 0-10. Top results from knowledge base. Default: 10</small>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="retrieval_method" class="form-label">Retrieval Method</label>
                    <select
                        id="retrieval_method"
                        name="retrieval_method"
                        class="form-control @error('retrieval_method') is-invalid @enderror"
                    >
                        <option value="none" {{ old('retrieval_method', $retrievalMethod) === 'none' ? 'selected' : '' }}>None - Uses the original query as-is</option>
                        <option value="rewrite" {{ old('retrieval_method', $retrievalMethod) === 'rewrite' ? 'selected' : '' }}>Rewrite - Refines the query for better retrieval</option>
                        <option value="step_back" {{ old('retrieval_method', $retrievalMethod) === 'step_back' ? 'selected' : '' }}>Step Back - Broadens the query for more context</option>
                        <option value="sub_queries" {{ old('retrieval_method', $retrievalMethod) === 'sub_queries' ? 'selected' : '' }}>Sub Queries - Splits into multiple focused queries</option>
                    </select>
                    @error('retrieval_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <small class="form-text text-muted">Strategy for retrieval-augmented generation. Default: None</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="border-radius: 0;">Save settings</button>
            </form>
        </div>
    </div>
</div>

<style>
    .form-range {
        width: 100%;
        height: 1.5rem;
        padding: 0;
        background-color: transparent;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .form-range:focus {
        outline: none;
    }
    .form-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 1rem;
        height: 1rem;
        background-color: #0d6efd;
        border: 0;
        border-radius: 0;
        cursor: pointer;
    }
    .form-range::-moz-range-thumb {
        width: 1rem;
        height: 1rem;
        background-color: #0d6efd;
        border: 0;
        border-radius: 0;
        cursor: pointer;
    }
    .form-range::-webkit-slider-runnable-track {
        width: 100%;
        height: 0.5rem;
        color: transparent;
        cursor: pointer;
        background-color: #dee2e6;
        border-color: transparent;
    }
    .form-range::-moz-range-track {
        width: 100%;
        height: 0.5rem;
        color: transparent;
        cursor: pointer;
        background-color: #dee2e6;
        border-color: transparent;
    }
</style>
@endsection

