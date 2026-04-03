@extends('livewire.agreements.migrated.frontend.main_master')
@section('title', 'Timing of Festive Season - Motorcycle Rental London, Tooting, Sutton, Catford, UK')
@section('content')


<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Timing of Festive Season</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="{{ route('festival.note') }}">Festive Season Note</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

    <section class="pt-3">
    <div class="container ">
        <p class=""><strong>Note: On 24th Dec (9am-1pm), Closed on 25th & 26th Dec, 31st Dec (9am-1pm), Closed on 1st & 2nd Jan, Operations resume on 3rd Jan.</strong></p>
                <br>
        <h2>Opening Times</h2>
        <p>We will be open as usual during the festive season weeks, with the following opening times:</p>
       <!-- Bootstrap Table for 2024 Festive Season Operation Schedule -->
<style>
    .table-striped > tbody > tr:nth-of-type(2n+1) > *{
        color: black !important;
    }
</style>
       <!-- Bootstrap Table for 2024 Festive Season Operation Schedule -->
<table class="table table-bordered table-striped" style="max-width: 500px; ">
    <thead>
        <tr>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>23rd</td>
            <td>9am</td>
            <td>6pm</td>
        </tr>
        <tr>
            <td>24th</td>
            <td>9am</td>
            <td>1pm</td>
        </tr>
        <tr class="text-white" style="background-color: #960202;color:white !important;">
            <td style="color:#fff !important;">25th</td>
            <td colspan="2" class="text-left" style="color:#fff !important;">Closed</td>
        </tr>
        <tr class="text-white" style="background-color: #960202;color:white !important;">
            <td style="color:#fff !important;">26th</td>
            <td colspan="2" class="text-left" style="color:#fff !important;">Closed</td>
        </tr>
        <tr>
            <td>27th</td>
            <td>9am</td>
            <td>6pm</td>
        </tr>
        <tr>
            <td>28th</td>
            <td>9am</td>
            <td>6pm</td>
        </tr>
        <tr>
            <td>30th</td>
            <td>9am</td>
            <td>6pm</td>
        </tr>
        <tr>
            <td>31st</td>
            <td>9am</td>
            <td>1pm</td>
        </tr>
        <tr class="text-white" style="background-color: #960202;color:white !important;">
            <td style="color:#fff !important;">1st</td>
            <td colspan="2" class="text-left" style="color:#fff !important;">Closed</td>
        </tr>
        <tr class="text-white" style="background-color: #960202;color:white !important;">
            <td style="color:#fff !important;">2nd</td>
            <td colspan="2" class="text-left" style="color:#fff !important;">Closed</td>
        </tr>
        <tr>
            <td>3rd</td>
            <td>9am</td>
            <td>6pm</td>
        </tr>
        <tr>
            <td>4th</td>
            <td>9am</td>
            <td>6pm</td>
        </tr>
    </tbody>
</table>



    </div>
    </section>
@stop
