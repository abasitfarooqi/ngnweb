@extends('olders.frontend.main_master')

@section('title', 'Return Policy - Motorcycle Rental London, Tooting, Sutton, Catford, UK')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Return Policy</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/return-policy">Return Policy</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<style>
    table.table-bordered{
        margin-top: 10px;
        border: 2px solid #000;
    }
    table.table-bordered tr td{
        border: 2px solid #000;
    }
    table.table-bordered tr th{
        border: 2px solid #000;
    }
</style>

<section class="blog-posts style1">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="post-wrap style1">

                    <article class="post clearfix">
                        <div class="content-post">
                            <div class="entry-post">
                                <h1 class="mt-1">Returns Policy</h1>

                                {{-- <h2 class="mt-2">Shipping Charges</h2>
                                <p>At Neguinho Motors Ltd, we strive to offer fair and transparent shipping rates:</p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Shipping Method</th>
                                            <th>Orders Under £70.00</th>
                                            <th>Orders Over £70.00</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Royal Mail</td>
                                            <td>£3.50</td>
                                            <td>Free</td>
                                        </tr>
                                        <tr>
                                            <td>Premier - Next Business Day</td>
                                            <td>£8.95</td>
                                            <td>Free</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <h2 class="mt-2">Returns Policy</h2> --}}
                                <p>Neguinho Motors Ltd aims to provide high-quality goods that meet or exceed your expectations. However, if you are not completely satisfied with your purchase or experience any issues, our return policy is as follows:</p>

                                <h3 class="mt-1">Eligibility for Returns</h3>
                                <p>To qualify for a return, the following conditions must be met:</p>
                                <ul>
                                    <li>Goods must be returned within <strong>14 days</strong> of receiving your order.</li>
                                    <li>Items must be unused, in their original condition, and returned with all packaging, manuals, and accessories.</li>
                                    <li>Goods must be in a condition fit for resale and without any additional work required.</li>
                                </ul>

                                <h3 class="mt-1">How to Initiate a Return</h3>
                                <ol>
                                    <li>Contact us within 14 days at <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a> or call <strong>0208 314 1498</strong> to request a return authorization.</li>
                                    <li>Once authorized, we will provide you with a return shipping label and further instructions.</li>
                                    <li>Pack the item securely in its original packaging, ensuring all documentation and accessories are included.</li>
                                    <li>Ship the item back to us using the provided shipping label.</li>
                                </ol>

                                <h3 class="mt-1">Refunds</h3>
                                <p>Refunds will be processed under the following conditions:</p>
                                <ul>
                                    <li>Once the returned item is received and inspected, you will be notified about the status of your refund.</li>
                                    <li>If approved, your refund will be credited to the original payment method within <strong>5-7 business days</strong>.</li>
                                    <li>Shipping fees for orders under £70.00 are non-refundable unless the return is due to a fault or error on our part.</li>
                                </ul>

                                <h3 class="mt-1">Exchanges</h3>
                                <p>If you wish to exchange an item for a different size or model:</p>
                                <ul>
                                    <li>Contact our team within 14 days of receiving the item.</li>
                                    <li>Ensure the item meets the eligibility conditions for returns (unused and in its original condition).</li>
                                    <li>Exchanges are subject to stock availability, and any price differences will be adjusted accordingly.</li>
                                </ul>

                                <h3 class="mt-1">Damaged or Faulty Items</h3>
                                <p>In the rare instance of receiving damaged or faulty items:</p>
                                <ul>
                                    <li>Report the issue to us within <strong>48 hours</strong> of receiving the delivery.</li>
                                    <li>Sign the delivery note as “damaged” if the damage is visible upon receipt.</li>
                                    <li>We will arrange a replacement or refund after inspecting the returned item. Return shipping costs will be covered by Neguinho Motors Ltd.</li>
                                </ul>

                                <h3 class="mt-1">Cooling-Off Period</h3>
                                <p>Under UK consumer law, you have a statutory right to a 14-day “cooling-off” period:</p>
                                <ul>
                                    <li>During this period, you can return unused goods without providing a reason.</li>
                                    <li>You are responsible for return shipping costs unless the goods are faulty or incorrect.</li>
                                </ul>

                                <h3 class="mt-1">Discretionary Returns</h3>
                                <p>We reserve the right to exercise discretion in cases not explicitly covered by this policy. Considerations may include:</p>
                                <ul>
                                    <li>The condition and use of the returned goods.</li>
                                    <li>Whether the goods are bespoke, perishable, or non-resalable once opened.</li>
                                </ul>

                                <h2 class="mt-2">Contact Us</h2>
                                <p>If you have questions or require further assistance, please reach out to our team:</p>
                                <ul>
                                    <li>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></li>
                                    <li>Phone: <strong>0208 314 1498</strong></li>
                                    <li>Address: 9-13 Unit 1179 Catford Hill, London, SE6 4NU</li>
                                </ul>
                            </div>
                        </div><!-- /.content-post -->
                    </article><!-- /.post -->

                    <article class="post clearfix">

                </div><!-- /.post-wrap -->

            </div><!-- /.col-md-9 -->
            <div class="col-md-3">
                @include('olders.frontend.legals.sidenav.legal-sidenav')
            </div><!-- /.col-md-3 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.blog posts -->
@stop
