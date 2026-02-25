const scrollAmount = 300; // Fixed amount to scroll left or right

    document.querySelector('.scroll-left').addEventListener('click', function() {
        const scrollableContainer = document.querySelector('.scrollable-container');
        scrollableContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        setTimeout(updateScrollerPosition, scrollAmount); // Delay to allow for smooth scrolling to complete
    });

    document.querySelector('.scroll-right').addEventListener('click', function() {
        const scrollableContainer = document.querySelector('.scrollable-container');
        scrollableContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        setTimeout(updateScrollerPosition, scrollAmount); // Delay to allow for smooth scrolling to complete
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

    document.querySelectorAll('.tbtn[data-toggle="tab"]').forEach(button => {
        button.addEventListener('click', function() {
            // Hide all hover images and show main images
            document.querySelectorAll('.hover-image').forEach(img => img.style.display = 'none');
            document.querySelectorAll('.main-image').forEach(img => img.style.display = 'block');

            // Show the hover image for the clicked tab
            const tabId = this.getAttribute('href').substring(1);
            const item = document.querySelector(`.item .tbtn[data-toggle="tab"][href="#${tabId}"]`).closest('.item');
            item.querySelector('.hover-image').style.display = 'block';
            item.querySelector('.main-image').style.display = 'none';

            // Uncheck all toggle switches
            document.querySelectorAll('.toggle-checkbox').forEach(cb => cb.checked = false);
        });
    });

    document.querySelectorAll('.tbtn-primary[data-toggle="tab"]').forEach(function(button) {
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

