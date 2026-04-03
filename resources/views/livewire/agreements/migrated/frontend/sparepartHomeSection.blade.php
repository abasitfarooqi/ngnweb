<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    .scrollable-container {
        display: flex;
        overflow-x: auto; /* Hide the scrollbar */
        white-space: nowrap;
    }
    .scrollable-container::-webkit-scrollbar {
        display: none; /* Hide scrollbar for WebKit browsers */
    }
    .scrollable-container {
        -ms-overflow-style: none;  /* Hide scrollbar for IE and Edge */
        scrollbar-width: none;  /* Hide scrollbar for Firefox */
    }
    .item {
        flex: 0 0 auto;
        width: 22%;
        box-sizing: border-box;
        padding: 1rem;
        /* border: 1px solid #ddd; */
        margin-right: 1rem;

    }
    .custom-scrollbar {
        position: relative;
    /* width: 100%; */
    /* height: 10px; */
    padding: 3px 0;
    border-radius: 20px;
    background-color: #f1f1f1;
    /* border: 2px solid black;*/
    }
    .scroller {
    position: absolute;
    background-color: red;
    border-radius: 20px;
    padding: 3px 120px;
    top: 0px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.scroller:hover,
.scroller:focus,
.scroller:active {
    padding: 5.6px 120px;
    top: -3px;
}
    .image-container {
    position: relative;
    width: 100%;
    height: auto;
}

.main-image,
.hover-image {
    width: 100%;
    height: auto;
    display: block;
}

.hover-image {
    display: none; /* Hidden by default */
}

.icon_circle {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
}

.icon_circle .toggle-switch {
    font-size: 24px;
    color: #000;
    cursor: pointer;
}

/* Toggle Switch Style */
.toggle-switch {
    display: inline-block;
    cursor: pointer;
}

.toggle-switch input {
    display: none;
}

.toggle-switch .slider {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
}

.slider.round {
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 12px;
    width: 12px;
    border-radius: 50%;
    background: #fff;
    transition: .4s;
}

input:checked + .slider:before {
    transform: translateX(14px);
}

.slider {
    background-color: #ccc;
}

input:checked + .slider {
    background-color: #2196F3;
}
/* Media Queries */
@@media (max-width: 576px) {
        .item {
            width: 100%;
        }
    }
    @@media (min-width: 577px) and (max-width: 768px) {
        .item {
            width: 50%;
        }
    }
/* -- 02 */
</style>
<div class="">
    <div class="text-center">

    <h2 class="fw-600" style="font-size: 2.2em;margin: 0;padding: 15px 0 10px 0;">Motorcycles for Sale</h2>
    </div>
    <div class="scroll-buttons mt-2" style="padding-left:3rem;padding-right:3rem;">
        <button class="btn ngn-btn mb-2 scroll-left"><i class="fa fa-chevron-left mx-2"></i></button>
        <button class="btn ngn-btn mb-2 scroll-right"><i class="fa fa-chevron-right mx-2"></i></button>
    </div>
    <div class="scrollable-container ">
        <!-- Tab 1 -->
<!-- Tab 1 -->
<div class="item"><a class="tbtn tbtn-primary  opacityx active" data-bs-toggle="tab" href="#tab1">
    <div class="image-container">
        <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
        <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
    </div>
    <h5>MT Braker Crash SV A8 Matt White Pink Purple</h5>
    <p style="color:black;font-size:18px;">Price: £3100.00</p>


</a></div>

<!-- Tab 2 -->
<div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab2">
    <div class="image-container">
        <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
        <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
    </div>
    <h5>Used HONDA SH</h5>
    <p style="color:black;font-size:18px;">Price: £1990.00</p>
    {{-- <strong><p>Reg No.: ****HGD</p>
    <p>Year: 2020</p>
    <p>Engine: 125</p>
    <p>Color: RED</p>
    <p>Milage: 80011</p>
    </strong> --}}
</a></div>

<!-- Tab 3 -->
<div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab3">
    <div class="image-container">
        <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
        <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
    </div>
    <h5>Used VOLKSWAGEN</h5>
    <p style="color:black;font-size:18px;">Price: £1400.00</p>
    {{-- <strong><p>Reg No.: ****XEL</p>
    <p>Year: 2008</p>
    <p>Engine: 3200</p>
    <p>Color: BLUE</p>
    <p>Milage: 200</p>
    </strong> --}}
</a></div>


    <!-- Tab 4 -->
    <div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab4">
        <div class="image-container">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
        </div>
        {{-- - --}}
        <h5>Title 4</h5>
        <p>Price: $160</p>
        </a>
    </div>

    <!-- Tab 5 -->
    <div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab5">
        <div class="image-container">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
        </div>
        {{-- - --}}
        <h5>Title 5</h5>
        <p>Price: $180</p>
        </a>
    </div>

    <!-- Tab 6 -->
    <div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab6">
        <div class="image-container">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
        </div>
        {{-- - --}}
        <h5>Title 6</h5>
        <p>Price: $200</p>
        </a>
    </div>

    <!-- Tab 7 -->
    <div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab7">
        <div class="image-container">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
        </div>
        {{-- - --}}
        <h5>Title 7</h5>
        <p>Price: $220</p>
        </a>
    </div>

    <!-- Tab 8 -->
    <div class="item"><a class="tbtn tbtn-primary opacityx " data-bs-toggle="tab" href="#tab8">
        <div class="image-container">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="main-image">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/1ScCCn41MHKOZZxGnHDjTb/64e0a123c005fbc96d4cd5967e86e674/DesertX-Borse-MY25-Model-Blocks-3-4-630x390.png" class="hover-image">
        </div>
        {{-- - --}}
        <h5>Title 8</h5>
        <p>Price: $240</p>
        </a>
    </div>
    </div>



    <div class="p-5" style="padding-top:inherit !important;padding-bottom:inherit !important;">
        <div class="custom-scrollbar mt-2 ">
            <div class="scroller"></div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div class="tab-content">
        <!-- Tab Contents -->
<div id="tab1" class="tab-pane fade show active">
    <div class="row">
        <div class="col-md-4">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="img-fluid">
        </div>
        <div class="col-md-8">
            <h3 style="font-weight: bold; color: #e81932;font-size:19px;">Used RENAULT VISION</h3>
            <p style="margin: 5px 0;font-weight:600;color:black;">Reg No.: ****XEX</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Year: 2016</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Engine: 1618</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Color: BLACK</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Milage: 2000</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Price: £3100</p>
            <p style="margin-top: 10px;font-size:15px;color:black;">
                <strong>CALL NOW FOR ENQUIRY 02083141498</strong>
            </p>
            <button class="btn btn-primary">Add to Cart</button>
            <button class="btn btn-danger">Sold</button>

        </div>
    </div>
</div>

<div id="tab2" class="tab-pane fade">
    <div class="row">
        <div class="col-md-4">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="img-fluid">
        </div>
        <div class="col-md-8">
            <h3 style="font-weight: bold; color: #e81932;font-size:19px;">Used HONDA SH</h3>
            <p style="margin: 5px 0;font-weight:600;color:black;">Sold</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Reg No.: ****HGD</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Year: 2020</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Engine: 125</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Color: RED</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Milage: 80011</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Price: £1990</p>
            <button class="btn btn-primary">Add to Cart</button>
            <button class="btn btn-danger">Sold</button>
        </div>
    </div>
</div>

<div id="tab3" class="tab-pane fade">
    <div class="row">
        <div class="col-md-4">
            <img loading="lazy" src="https://images.ctfassets.net/x7j9qwvpvr5s/5TL8zEeLTItLrl4gKjhinD/b213cc3baa6b78e2f8d51e3d52ad2101/DesertX-Borse-MY25-Model-Blocks-630x390.png" class="img-fluid">
        </div>
        <div class="col-md-8">
            <h3 style="font-weight: bold; color: #e81932;font-size:19px;">Used VOLKSWAGEN VOLKSWAGEN</h3>
            <p style="margin: 5px 0;font-weight:600;color:black;">Reg No.: ****XEL</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Year: 2008</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Engine: 3200</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Color: BLUE</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Milage: 200</p>
            <p style="margin: 5px 0;font-weight:600;color:black;">Price: £1400</p>
            <button class="btn btn-primary">Add to Cart</button>
            <button class="btn btn-danger">Sold</button>
        </div>
    </div>
</div>

        <div id="tab4" class="tab-pane fade">
            <h3>Tab 4 Content</h3>
            <p>Details for Item 4.</p>
        </div>
        <div id="tab5" class="tab-pane fade">
            <h3>Tab 5 Content</h3>
            <p>Details for Item 5.</p>
        </div>
        <div id="tab6" class="tab-pane fade">
            <h3>Tab 6 Content</h3>
            <p>Details for Item 6.</p>
        </div>
        <div id="tab7" class="tab-pane fade">
            <h3>Tab 7 Content</h3>
            <p>Details for Item 7.</p>
        </div>
        <div id="tab8" class="tab-pane fade">
            <h3>Tab 8 Content</h3>
            <p>Details for Item 8.</p>
        </div>
        <div id="tab9" class="tab-pane fade">
            <h3>Tab 9 Content</h3>
            <p>Details for Item 9.</p>
        </div>
        <div id="tab10" class="tab-pane fade">
            <h3>Tab 10 Content</h3>
            <p>Details for Item 10.</p>
        </div>
        <div id="tab11" class="tab-pane fade">
            <h3>Tab 11 Content</h3>
            <p>Details for Item 11.</p>
        </div>
        <div id="tab12" class="tab-pane fade">
            <h3>Tab 12 Content</h3>
            <p>Details for Item 12.</p>
        </div>
    </div>
</div>

<br>
{{--
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}

<script>
    const scrollAmount = 300; // Fixed amount to scroll left or right

    document.querySelector('.scroll-left').addEventListener('click', function() {
        const scrollableContainer = document.querySelector('.scrollable-container');
        scrollableContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        setTimeout(updateScrollerPosition, 300); // Delay to allow for smooth scrolling to complete
    });

    document.querySelector('.scroll-right').addEventListener('click', function() {
        const scrollableContainer = document.querySelector('.scrollable-container');
        scrollableContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        setTimeout(updateScrollerPosition, 300); // Delay to allow for smooth scrolling to complete
    });

    // Handle clicks on the custom scrollbar
    document.querySelector('.custom-scrollbar').addEventListener('click', function(event) {
        const customScrollbar = document.querySelector('.custom-scrollbar');
        const scroller = document.querySelector('.scroller');
        const scrollableContainer = document.querySelector('.scrollable-container');
        const scrollbarWidth = scrollableContainer.scrollWidth - scrollableContainer.clientWidth;
        const rightEdge = customScrollbar.offsetWidth - scroller.offsetWidth;
        const clickX = event.clientX - customScrollbar.getBoundingClientRect().left;

        // Move scroller to the clicked position
        let newLeft = clickX - (scroller.offsetWidth / 2);
        if (newLeft < 0) newLeft = 0;
        if (newLeft > rightEdge) newLeft = rightEdge;

        scroller.style.left = newLeft + 'px';
        const scrollPercent = newLeft / rightEdge;
        scrollableContainer.scrollLeft = scrollPercent * scrollbarWidth;
    });

    function updateScrollerPosition() {
        const scrollableContainer = document.querySelector('.scrollable-container');
        const scroller = document.querySelector('.scroller');
        const scrollbarWidth = scrollableContainer.scrollWidth - scrollableContainer.clientWidth;
        const customScrollbar = document.querySelector('.custom-scrollbar');
        const rightEdge = customScrollbar.offsetWidth - scroller.offsetWidth;
        const scrollPercent = scrollableContainer.scrollLeft / scrollbarWidth;
        scroller.style.left = scrollPercent * rightEdge + 'px';
    }

    const scroller = document.querySelector('.scroller');
    scroller.onmousedown = function(event) {
        const customScrollbar = document.querySelector('.custom-scrollbar');
        const rightEdge = customScrollbar.offsetWidth - scroller.offsetWidth;

        let shiftX = event.clientX - scroller.getBoundingClientRect().left;

        document.onmousemove = function(event) {
            let newLeft = event.clientX - shiftX - customScrollbar.getBoundingClientRect().left;
            if (newLeft < 0) newLeft = 0;
            if (newLeft > rightEdge) newLeft = rightEdge;

            scroller.style.left = newLeft + 'px';

            const scrollableContainer = document.querySelector('.scrollable-container');
            const scrollbarWidth = scrollableContainer.scrollWidth - scrollableContainer.clientWidth;
            scrollableContainer.scrollLeft = (newLeft / rightEdge) * scrollbarWidth;
        };

        document.onmouseup = function() {
            document.onmousemove = document.onmouseup = null;
        };
    };

    scroller.ondragstart = function() {
        return false;
    };

    document.querySelectorAll('.tbtn[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('click', function() {
            // Hide all hover images and show main images
            document.querySelectorAll('.hover-image').forEach(img => img.style.display = 'none');
            document.querySelectorAll('.main-image').forEach(img => img.style.display = 'block');

            // Show the hover image for the clicked tab
            const tabId = this.getAttribute('href').substring(1);
            const item = document.querySelector(`.item .tbtn[data-bs-toggle="tab"][href="#${tabId}"]`).closest('.item');
            item.querySelector('.hover-image').style.display = 'block';
            item.querySelector('.main-image').style.display = 'none';

            // Uncheck all toggle switches
            document.querySelectorAll('.toggle-checkbox').forEach(cb => cb.checked = false);
        });
    });

    document.querySelectorAll('.tbtn-primary[data-bs-toggle="tab"]').forEach(function(button) {
        button.addEventListener('click', function(event) {
            const target = this.getAttribute('href');

            document.querySelectorAll('.tab-pane').forEach(function(tabPane) {
                tabPane.classList.remove('show', 'active');
            });
            document.querySelector(target).classList.add('show', 'active');

            // Toggle images
            const item = this.closest('.item');
            const mainImage = item.querySelector('.main-image');
            const hoverImage = item.querySelector('.hover-image');
            mainImage.style.display = 'none';
            hoverImage.style.display = 'block';

            // Toggle icons
            const iconCircle = item.querySelector('.icon_circle');
            iconCircle.classList.toggle('active');

            event.preventDefault();
        });
    });
// Toggle switch functionality
document.querySelectorAll('.toggle-switch').forEach(switchElem => {
    switchElem.addEventListener('click', function() {
        const checkbox = this.querySelector('.toggle-checkbox');
        checkbox.checked = !checkbox.checked;
    });
});
    // --01
</script>
