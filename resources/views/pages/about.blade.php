@extends('layouts.kars')

@section('title', $page->title ?? 'About Us')
@section('meta_description', $page->meta_description ?? 'Learn more about G&M Auto Parts and how we supply quality used auto parts across New Zealand.')

@section('content')
    <div class="breadcumb-wrapper" data-bg-src="/images/page-headers/header-1.jpg" data-overlay="black" data-opacity="3">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">About Us</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>About Us</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="about-1-wrapper space" id="about-sec">
        <div class="container">
            <div class="row gy-40 gx-60 flex-wrap-reverse justify-content-center">
                <div class="col-xl-7">
                    <div class="img-box1 about-2">
                        <div class="img1">
                            <img class="tilt-active" src="{{ asset('images/About Page/312x300.jpg') }}" alt="G&amp;M Auto Parts">
                        </div>
                        <div class="img2">
                            <img class="tilt-active" src="{{ asset('images/About Page/370x291.jpg') }}" alt="G&amp;M Auto Parts">
                        </div>
                        <div class="img3">
                            <img src="{{ asset('images/About Page/424x461.jpg') }}" alt="G&amp;M Auto Parts">
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="title-area mb-25 ps-xl-4">
                        <span class="sub-title style-2">Welcome to G&amp;M Auto Parts</span>
                        <h2 class="sec-title">About G&amp;M Auto Spares Ltd</h2>
                        <p class="sec-text">G&amp;M Auto Spares Ltd is a dismantling business situated in Te Awamutu, Waikato, New Zealand. We are one of New Zealand's leading dismantlers of General Motors vehicles with over 2 acres and approximately 700 cars in stock.</p>
                        <p class="sec-text">Starting as a small yard in 1983 by Graeme &amp; Mary, they have now been in business for 30 years and have grown considerably. We also incorporate other popular makes and models.</p>
                        <div class="client-bottom-wrap">
                            <div class="client-wrap">
                                <div class="thumb">
                                    <img src="/kars/img/about/about-1.1-client.png" alt="Founder portrait">
                                </div>
                                <div class="text-wrap">
                                    <h4 class="box-title">G&amp;M Auto Parts Team</h4>
                                    <p>Used Parts Specialists</p>
                                </div>
                            </div>
                            <div class="sing-thumb">
                                <img src="/kars/img/about/about-1.1-sing.png" alt="Signature">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-xl-end">
                <div class="col-xl-8">
                    <div class="counter-card-wrap style-2-inner">
                        <div class="counter-card">
                            <div class="box-icon">
                                <img src="/kars/img/icon/counter_1.1_1.svg" alt="Icon">
                            </div>
                            <div class="media-body">
                                <h4 class="box-number"><span class="counter-number">2500</span><span class="plus-simple">+</span></h4>
                                <p class="box-text">Parts Listed</p>
                            </div>
                        </div>
                        <div class="counter-card">
                            <div class="box-icon">
                                <img src="/kars/img/icon/counter_1.1_2.svg" alt="Icon">
                            </div>
                            <div class="media-body">
                                <h4 class="box-number"><span class="counter-number">300</span><span class="plus-simple">+</span></h4>
                                <p class="box-text">Vehicles Dismantled</p>
                            </div>
                        </div>
                        <div class="counter-card">
                            <div class="box-icon">
                                <img src="/kars/img/icon/counter_1.1_3.svg" alt="Icon">
                            </div>
                            <div class="media-body">
                                <h4 class="box-number"><span class="counter-number">98</span><span class="plus-simple">%</span></h4>
                                <p class="box-text">Customer Satisfaction</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="title-area text-center mb-35">
                        <h2 class="sec-title">Vehicle Range and Brands</h2>
                        <p class="sec-text">Our range comprises vehicles from 1997-2013.</p>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="why-card-2 h-100">
                                <div class="box-content">
                                    <h3 class="box-title">GM</h3>
                                    <p class="box-text">HSV, Commodore, Astra, Vectra, Barina, Epica, Cruze, Captiva, Viva, Opel, Combo, Adventra, Crewman</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="why-card-2 h-100">
                                <div class="box-content">
                                    <h3 class="box-title">Chrysler</h3>
                                    <p class="box-text">300C, PT Cruiser, Voyager Van</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="why-card-2 h-100">
                                <div class="box-content">
                                    <h3 class="box-title">Hyundai</h3>
                                    <p class="box-text">Late Sonata, Elantra, 4WD Tucson and Santa Fe</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="why-card-2 h-100">
                                <div class="box-content">
                                    <h3 class="box-title">Daihatsu</h3>
                                    <p class="box-text">Terios, Charade, Sirion, YRV</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="why-card-2 h-100">
                                <div class="box-content">
                                    <h3 class="box-title">Suzuki</h3>
                                    <p class="box-text">Late Swifts, SX4, Aerio</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="title-area mt-35 mb-0">
                        <p class="sec-text mb-2">We have new stock arriving daily, so if we cannot supply today we may be able to in the near future.</p>
                        <p class="sec-text mb-2">We ship NZ-wide using all major courier and freight companies, and also ship overseas.</p>
                        <p class="sec-text mb-0">We offer fast and friendly service from our knowledgeable staff.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="why-sec-2" id="why-sec">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-7 col-lg-9">
                    <div class="title-area text-center">
                        <h2 class="sec-title">Why Choose Us</h2>
                        <p>We combine deep dismantling experience with practical customer support to help you source the right used part the first time.</p>
                    </div>
                </div>
            </div>
            <div class="row gy-30 align-items-center">
                <div class="col-lg-6 col-xl-3">
                    <div class="why-card-2">
                        <div class="box-icon">
                            <img src="/kars/img/icon/why_card_2_1.svg" alt="Icon">
                        </div>
                        <div class="box-content">
                            <h3 class="box-title">Fresh Local Stock</h3>
                            <p class="box-text">New dismantled vehicles are added regularly, giving you better part availability.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3">
                    <div class="why-card-2">
                        <div class="box-icon">
                            <img src="/kars/img/icon/why_card_2_2.svg" alt="Icon">
                        </div>
                        <div class="box-content">
                            <h3 class="box-title">Fair, Transparent Pricing</h3>
                            <p class="box-text">Clear pricing on quality-tested used parts to help reduce repair costs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3">
                    <div class="why-card-2">
                        <div class="box-icon">
                            <img src="/kars/img/icon/why_card_2_3.svg" alt="Icon">
                        </div>
                        <div class="box-content">
                            <h3 class="box-title">Fitment Guidance</h3>
                            <p class="box-text">Our team helps confirm compatibility before purchase to avoid delays.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3">
                    <div class="why-card-2">
                        <div class="box-icon">
                            <img src="/kars/img/icon/why_card_2_4.svg" alt="Icon">
                        </div>
                        <div class="box-content">
                            <h3 class="box-title">Nationwide Delivery</h3>
                            <p class="box-text">Fast dispatch options across New Zealand for workshops and retail customers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="th-blog-wrapper blog-details space">
        <div class="container">
            <div class="row">
                <div class="col-xxl-5 col-lg-5">
                    <aside class="sidebar-area">
                        <div class="faq-widget-wrap">
                            <div class="widget faq-help">
                                <h3>Need Help?</h3>
                                <p class="box-text mb-4">Not sure if a part will fit? Send us your vehicle details and we will help confirm the right match.</p>
                                <p class="box-text mb-4">For urgent enquiries, please call us directly and our team will assist as quickly as possible.</p>
                            </div>
                            <div class="widget">
                                <h3 class="widget_title">Quick Contact</h3>
                                <div class="contact-form-widget">
                                    <form action="{{ route('contact.submit') }}" method="post" class="newsletter-form">
                                        @csrf
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="name" placeholder="Your Name" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <textarea name="message" cols="30" rows="3" class="form-control" placeholder="Write Message..." required></textarea>
                                        </div>
                                        <div class="form-btn">
                                            <button type="submit" class="th-btn w-100">Send Enquiry</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
                <div class="col-xxl-7 col-lg-7">
                    <div class="th-faq-wrapper">
                        <h5 class="mb-3">About G&amp;M Auto Parts</h5>
                        <div class="accordion mb-4" id="faqAccordion1">
                            <div class="accordion-card stye-2">
                                <div class="accordion-header" id="collapse-item-1-1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1-1" aria-expanded="true" aria-controls="collapse-1-1">
                                        What does G&amp;M Auto Parts do?
                                    </button>
                                </div>
                                <div id="collapse-1-1" class="accordion-collapse collapse show" aria-labelledby="collapse-item-1-1" data-bs-parent="#faqAccordion1">
                                    <div class="accordion-body">
                                        <p class="faq-text">We dismantle vehicles and supply quality used auto parts for a wide range of makes and models.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-card stye-2">
                                <div class="accordion-header" id="collapse-item-1-2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1-2" aria-expanded="false" aria-controls="collapse-1-2">
                                        Do you ship parts across New Zealand?
                                    </button>
                                </div>
                                <div id="collapse-1-2" class="accordion-collapse collapse" aria-labelledby="collapse-item-1-2" data-bs-parent="#faqAccordion1">
                                    <div class="accordion-body">
                                        <p class="faq-text">Yes. We can arrange delivery nationwide and help you choose the best shipping option for your order.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-card stye-2">
                                <div class="accordion-header" id="collapse-item-1-3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1-3" aria-expanded="false" aria-controls="collapse-1-3">
                                        Can you help confirm part compatibility?
                                    </button>
                                </div>
                                <div id="collapse-1-3" class="accordion-collapse collapse" aria-labelledby="collapse-item-1-3" data-bs-parent="#faqAccordion1">
                                    <div class="accordion-body">
                                        <p class="faq-text">Absolutely. Share your registration, VIN, or part number and we will help verify fitment before purchase.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($page?->content))
                            <h5 class="mb-3">More Information</h5>
                            <div class="accordion" id="faqAccordion2">
                                <div class="accordion-card stye-2">
                                    <div class="accordion-header" id="collapse-item-2-1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-2-1" aria-expanded="true" aria-controls="collapse-2-1">
                                            Additional details
                                        </button>
                                    </div>
                                    <div id="collapse-2-1" class="accordion-collapse collapse show" aria-labelledby="collapse-item-2-1" data-bs-parent="#faqAccordion2">
                                        <div class="accordion-body">
                                            <div class="faq-text">{!! $page->content !!}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
