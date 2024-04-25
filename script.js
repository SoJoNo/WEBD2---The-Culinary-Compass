document.addEventListener('DOMContentLoaded', function() {
    // Add animation to the navbar when scrolling
    window.addEventListener('scroll', function() {
        var navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scroll');
        } else {
            navbar.classList.remove('navbar-scroll');
        }
    });

    // Add animation to form inputs on focus
    var formInputs = document.querySelectorAll('input[type="text"], textarea');
    formInputs.forEach(function(input) {
        input.addEventListener('focus', function() {
            input.parentElement.classList.add('input-focus');
        });
        input.addEventListener('blur', function() {
            input.parentElement.classList.remove('input-focus');
        });
    });

    // Add animation to submit button on hover
    var submitButton = document.querySelector('input[type="submit"]');
    submitButton.addEventListener('mouseenter', function() {
        submitButton.classList.add('submit-hover');
    });
    submitButton.addEventListener('mouseleave', function() {
        submitButton.classList.remove('submit-hover');
    });

    // Add animation to navbar links on hover
    var navbarLinks = document.querySelectorAll('.navbar a');
    navbarLinks.forEach(function(link) {
        link.addEventListener('mouseenter', function() {
            link.classList.add('link-hover');
        });
        link.addEventListener('mouseleave', function() {
            link.classList.remove('link-hover');
        });
    });
});
