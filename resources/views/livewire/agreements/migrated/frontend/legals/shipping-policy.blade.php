@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Shipping Policy - Motorcycle Rental London, Tooting, Sutton, Catford, UK')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Shipping Policy</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/shipping-policy">Shipping Policy</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<section class="blog-posts style1">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="post-wrap style1">

                    <article class="post clearfix">
                        <div class="content-post">
                            <div class="entry-post">
                                <p>
                                    <strong>Introduction</strong>
                                </p>
                                <p class="mb-3">
                                    This privacy policy applies to this website, namely www.neguinhomotors.co.uk owned by Neguinho Motors and governs the privacy of its users.
                                </p>
                                <p class="mb-3">
                                    This website can be accessed by a number of other web URL’s owned by Neguinho Motors for example: www.neguinhomotors.co.uk
                                </p>
                                <p class="mb-3">
                                    The policy sets out the different areas where user privacy is concerned and outlines the obligations & requirements of the users and Neguinho Motors.
                                </p>
                                <p class="mb-3">
                                    Furthermore the way this website processes, stores and protects user data and information will also be detailed within this policy.
                                </p>
                                <p>
                                    <strong>Website Policy</strong>
                                </p>
                                <p class="mb-3">
                                    Neguinho Motors take a proactive approach to user privacy and ensure the necessary steps are taken to protect the privacy of its users throughout their visit to this site. This website complies with all UK national laws and requirements for user privacy.
                                </p>
                            </div>
                        </div><!-- /.content-post -->
                    </article><!-- /.post -->

                    <article class="post clearfix">

                </div><!-- /.post-wrap -->

            </div><!-- /.col-md-9 -->
            <div class="col-md-3">
                @include('livewire.agreements.migrated.frontend.legals.sidenav.legal-sidenav')
            </div><!-- /.col-md-3 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.blog posts -->
@stop
