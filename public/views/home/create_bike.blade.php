@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>Add Motorcycle</h1>
    <p class="lead">Only authenticated users can access this section.</p>

    <form method="post" action="/motorcycles">
        @csrf
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Registration" aria-label="default input example" name="registration" id="registration">
        </div>
        <div class="mb-3">
            <select class="form-select" aria-label="Default select example" name="make" id="make">
                <option value="" selected="selected">Select Make</option>
            </select>
        </div>
        <div class="mb-3">
            <select class="form-select" aria-label="Default select example" name="model" id="model">
                <option value="" selected="selected">Select Model</option>
            </select>
        </div>
        <div class="mb-3">
            <select class="form-select" aria-label="Default select example" name="year" id="year">
                <option value="" selected="selected">Select Year</option>
            </select>
        </div>
        <div class="mb-3">
            <select class="form-select" aria-label="Default select example" name="displacement" id="displacement">
                <option selected>CC</option>
                <option value="110">110</option>
                <option value="115">115</option>
                <option value="125">125</option>
                <option value="150">150</option>
                <option value="250">250</option>
                <option value="300">300</option>
                <option value="350">350</option>
                <option value="400">400</option>
                <option value="500">500</option>
                <option value="600">600</option>
                <option value="650">650</option>
                <option value="750">750</option>
                <option value="900">900</option>
                <option value="950">950</option>
                <option value="1000">1000</option>
                <option value="1200">1200</option>
            </select>
        </div>
        <div class="mb-3">
            <select class="form-select" aria-label="Default select example" name="colour" id="colour">
                <option selected>Select Colour</option>
                <option value="Grey">Grey</option>
                <option value="White">White</option>
                <option value="Red">Red</option>
                <option value="Blue">Blue</option>
                <option value="Black">Black</option>
                <option value="Black">Silver</option>
            </select>
        </div>
        <div class="mb-3">
            <select class="form-select" aria-label="Default select example" name="availability" id="availability">
                <option selected>Availability</option>
                <option value="Catford">Catford</option>
                <option value="Tooting">Tooting</option>
                <option value="Sold">Sold</option>
                <option value="Rented">Rented</option>
                <option value="Unallocated">Unallocated</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    @endauth

    @guest
    <h1>Homepage</h1>
    <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
    @endguest
</div>
@endsection