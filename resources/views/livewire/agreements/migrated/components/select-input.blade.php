{{-- resources/views/components/select-input.blade.php --}}
<div>
    <select wire:model.live="{{ $model }}" style="{{ $style_css }}">
        <option value="">Select a service type</option>
        @foreach ($options as $option)
            <option value="{{ $option->value }}">{{ $option->value }}</option>
        @endforeach
    </select>
</div>
