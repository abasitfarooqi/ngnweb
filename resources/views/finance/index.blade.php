@extends('frontend.main_master')

@section('title', 'Motorbikes on Instalment - Finance for Motorcycles in London, Catford, Tooting and Sutton')

@section('content')
    <style>
        .heading-xxlarge {
            letter-spacing: -.1875rem;
            font-size: 5rem;
            font-weight: 900;
            line-height: 1.2;
            color: rgb(125, 121, 121);
        }

        .layout232_component {
            z-index: 999;
            grid-column-gap: 3rem;
            grid-row-gap: 4rem;
            grid-template-rows: auto;
            grid-template-columns: 1fr 1fr 1fr;
            grid-auto-columns: 1fr;
            align-items: start;
            justify-items: start;
            display: grid;
            position: relative;
        }

        .w-layout-grid {
            grid-row-gap: 16px;
            grid-column-gap: 16px;
            grid-template-rows: auto auto;
            grid-template-columns: 1fr 1fr;
            grid-auto-columns: 1fr;
            display: grid;
        }

        * {
            box-sizing: border-box;
        }

        .contact_item,
        .benefits_item {
            display: flex;
        }

        .margin-bottom.margin-custom2,
        .margin-bottom.margin-xxlarge,
        .margin-bottom.margin-custom1,
        .margin-bottom.margin-xxhuge,
        .margin-bottom.margin-huge,
        .margin-bottom.margin-large,
        .margin-bottom.margin-medium,
        .margin-bottom.margin-xsmall,
        .margin-bottom.margin-0,
        .margin-bottom.margin-xlarge,
        .margin-bottom.margin-tiny,
        .margin-bottom.margin-small,
        .margin-bottom.margin-xhuge,
        .margin-bottom.margin-custom3,
        .margin-bottom.margin-xxsmall,
        .margin-bottom.margin-xsmall {
            margin-top: 0;
            margin-left: 0;
            margin-right: 0;
        }

        .w-checkbox-input {
            float: left;
            margin: 4px 0 0 -20px;
            line-height: normal
        }

        .w-checkbox-input--inputType-custom {
            border: 1px solid #ccc;
            border-radius: 2px;
            width: 12px;
            height: 12px
        }

        .w-checkbox-input--inputType-custom.w--redirected-checked {
            background-color: #3898ec;
            background-image: url(https://d3e54v103j8qbb.cloudfront.net/static/custom-checkbox-checkmark.589d534424.svg);
            background-position: 50%;
            background-repeat: no-repeat;
            background-size: cover;
            border-color: #3898ec
        }

        .w-checkbox-input--inputType-custom.w--redirected-focus {
            box-shadow: 0 0 3px 1px #3898ec
        }

        input[type="checkbox"] {
            display: inherit !important;
        }
    </style>

    <div class="main-finance-banner">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="banner-content">
                        <h1 class="heading-xxlarge ">Motorbikes on Instalment</h1>

                    </div>
                    <p style="font-size: 14px">Our finance team is here to help you with all your query related to
                        installment plan. <br> Call
                        on :
                        02083141498 (MON-SAT 09:00 to 18:00)</p>
                </div>
            </div>
        </div>
    </div>


    @include('frontend.body.newsletter')
@endsection
