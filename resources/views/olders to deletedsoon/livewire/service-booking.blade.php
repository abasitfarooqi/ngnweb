{{-- resources/views/livewire/service-booking.blade.php --}}
<style>
    .service-booking-form input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 12px;
    }

    /* UK VRM Styling */
    .uk-vrm-container {
        background: #FED71B;
        border: 3px solid #000;
        border-radius: 5px;
        padding: 0;
        display: inline-block;
        align-items: center;
        justify-content: center;
        width: 100%;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-image: linear-gradient(45deg, #FED71B 25%, #FFE03D 25%, #FFE03D 50%, #FED71B 50%, #FED71B 75%, #FFE03D 75%, #FFE03D 100%);
        background-size: 4px 4px;
    }

    .uk-vrm-input {
        width: 100%;
        height: 25px;
        padding-top: 12px !important;
        margin-top: 20px !important;
        /* Increased margin-top for more distance from the top */
        font-family: 'UKNumberPlate', 'CharlesWright', Arial, sans-serif;
        font-size: 22px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none !important;
        border-color: #FED71B !important;
        outline: #FED71B !important;
        background: #FED71B !important;
        border-radius: 0px !important;
        color: #FED71B !important;
        text-align: center;
        -webkit-text-fill-color: #000;
        box-shadow: #FED71B !important;
        margin: 0;
        appearance: none;
        -webkit-appearance: none;
        background-color: #FED71B !important;
    }

    .uk-vrm-input::placeholder {
        color: rgba(0, 0, 0, 0.2);
        font-weight: bold;
        letter-spacing: 2px;
        opacity: 0.5;
    }

    /* GB Band Styling */
    .uk-vrm-container::before {
        content: " GB ";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 35px !important;
        background: #0055a4;
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px;
        color: white !important;
        font-size: 14px !important;
        font-weight: bold !important;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0;
        border-right: 2px solid #003876;
        z-index: 1;
    }

    /* Remove any white background on autofill */
    .uk-vrm-input:-webkit-autofill,
    .uk-vrm-input:-webkit-autofill:hover,
    .uk-vrm-input:-webkit-autofill:focus,
    .uk-vrm-input:-webkit-autofill:active {
        transition: background-color 5000s ease-in-out 0s;
        -webkit-box-shadow: 0 0 0 30px #FED71B inset !important;
        -webkit-text-fill-color: #000 !important;
        border: none !important;
        outline: none !important;
    }

    /* Add embossed effect */
    .uk-vrm-input {
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
    }

    /* Remove inner shadow */
    .uk-vrm-container::after {
        display: none;
    }

    /* Remove focus outline */
    .uk-vrm-input:focus {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
</style>

<div class="container item-center"
    style="width: 90%; margin:10px; padding:10px; border: 1px solid #ccc; border-radius: 12px;">
    <h3 class="text-center">Service Booking</h3>
    <div>
        <form wire:submit="submit" class="text-center">
            <button type="button" wire:click="test" class="btn btn-sm btn-info mb-3">Test Button</button>

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="name">Name</label>
                <input type="text" id="name" wire:model="name"
                    style="margin-top: 10px !important; width: 300px !important;">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- VRM / container input etc --}}
            <div class="uk-vrm-container" style="width: 210px;">
                <input type="text" id="vrm" wire:model="vrm" class="uk-vrm-input"
                    style="margin-left: 12px !important; margin-top: 10px !important; padding-top: 10px !important; width: 190px !important; height: 30px !important  ; background-color: #FED71B !important; border-color: #FED71B !important;">
            </div>
            @error('vrm')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <div class="text-center"
                style="margin-top: 10px !important; max-width: 300px !important; margin-left: auto; margin-right: auto;">
                <small class="text-muted" style="display: block; text-align: center;">
                    VRM / Registration Number is help us identify your vehicle so we can get the right parts and service
                    for you.
                </small>
            </div>

            <div style="margin-top: 10px !important;">
                <label for="email">Email</label>
                <input type="email" id="email" wire:model="email"
                    style="margin-top: 10px !important; width: 300px !important;">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="phone">Phone</label>
                <input type="tel" id="phone" wire:model="phone"
                    style="margin-top: 10px !important; width: 300px !important;">
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="typeOfService">Type of Service</label>
                <x-select-input model="typeOfService" :options="\App\Enums\ServiceTypes::cases()"
                    style_css="line-height: 1.5 !important; margin-top: 10px !important; width: 300px !important;" />
                @error('typeOfService')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <br>
            <div class="text-center">
                <button type="submit" class="btn btn-success">
                    Submit Booking Enquiry
                </button>
            </div>
        </form>
    </div>
</div>
