
         // Initialize AOS (for other scroll animations)
    AOS.init({
        duration: 1000, // Animation duration in milliseconds
        // once: true,     // Whether animation should happen only once
    });

    // Function to apply Typewriter effect based on the class
    function applyTypewriterEffect(element) {
        var originalText = element.textContent;
        element.textContent = '';

        // Determine typing speed based on the class
        var delay;
        if (element.classList.contains('tw-low')) {
            delay = 100;
        } else if (element.classList.contains('tw-medium')) {
            delay = 75;
        } else if (element.classList.contains('tw-fast')) {
            delay = 50;
        } else if (element.classList.contains('tw-faster')) {
            delay = 25;
        } else if (element.classList.contains('tw-fastest')) {
            delay = 10;
        } else if (element.classList.contains('tw-fastestest')) {
            delay = 5;
        } else {
            delay = 75; // Default delay if no class matches
        }

        // Initialize the Typewriter
        var typewriter = new Typewriter(element, {
            loop: false,
            delay: delay,
            cursor: '|', // Custom cursor (optional)
        });

        // Apply the Typewriter effect
        typewriter
            .typeString(originalText)
            .callFunction(() => {
                // Hide the cursor after typing is done
                element.querySelector('.Typewriter__cursor').style.display = 'none';
            })
            .start();
    }

    // Intersection Observer callback function
    function handleIntersection(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                applyTypewriterEffect(entry.target);
                observer.unobserve(entry.target); // Stop observing once the animation is triggered
            }
        });
    }

    // Create a new Intersection Observer
    var observer = new IntersectionObserver(handleIntersection, {
        threshold: 0.5 // Trigger when 50% of the element is visible
    });

    // Select only elements with the specific Typewriter classes
    var elements = document.querySelectorAll('.tw-low, .tw-medium, .tw-fast, .tw-faster, .tw-fastest, .tw-fastestest');

    // Observe each element with the Typewriter classes for intersection
    elements.forEach(element => observer.observe(element));
