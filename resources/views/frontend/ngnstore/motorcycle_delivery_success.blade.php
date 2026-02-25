<!-- resources/views/frontend/ngnstore/motorcycle_delivery_success.blade.php -->
@extends('frontend.ngnstore.layouts.master')

@section('title', 'Order Success')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
        <!-- Success Message -->
        <div class="text-center">
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900">
                Quotation Request sent...!
            </h2>
            <div class="mt-4">
                <div class="h-1 w-24 bg-green-500 mx-auto rounded-full"></div>
            </div>
            <p class="mt-4 text-lg text-gray-600">
                Thank you for choosing our service! Your order has been successfully placed.
            </p>
            <p class="mt-2 text-sm text-gray-500">
                A confirmation email with the total cost and payment details has been sent to your provided email address.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 space-y-3">
            <a href="/" 
               class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                Back to Home
            </a>
            <!-- <a href="#" 
               class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                Track Your Order
            </a> -->
        </div>

        <!-- Contact Information -->
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="text-center text-sm text-gray-600">
                <p>Need Further Help? Contact our support team</p>
                <p class="mt-1 font-medium">0208 314 1498</p>
            </div>
        </div>
    </div>
</div>
@endsection