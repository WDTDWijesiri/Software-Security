document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('.order');

    form.addEventListener('submit', function (event) {
        let hasError = false;

        // Validate full name
        const fullName = document.querySelector('input[name="full-name"]');
        const nameError = document.querySelector('.error-name');
        nameError.textContent = ''; // Clear previous error

        if (!/^[a-zA-Z\s]+$/.test(fullName.value)) {
            nameError.textContent = 'Name can only contain letters and spaces.';
            hasError = true;
        } else if (fullName.value.length > 50) {
            nameError.textContent = 'Name is too long.';
            hasError = true;
        }

        // Validate phone number
        const contact = document.querySelector('input[name="contact"]');
        const contactError = document.querySelector('.error-contact');
        contactError.textContent = ''; // Clear previous error

        if (!/^\d{10}$/.test(contact.value)) {
            contactError.textContent = 'Please enter a valid phone number.';
            hasError = true;
        }

        // Validate email
        const email = document.querySelector('input[name="email"]');
        const emailError = document.querySelector('.error-email');
        emailError.textContent = ''; // Clear previous error

        if (!/\S+@\S+\.\S+/.test(email.value)) {
            emailError.textContent = 'Please enter a valid email address.';
            hasError = true;
        }

        // Validate address
        const address = document.querySelector('textarea[name="address"]');
        const addressError = document.querySelector('.error-address');
        addressError.textContent = ''; // Clear previous error

        if (address.value.trim() === '') {
            addressError.textContent = 'Address is required.';
            hasError = true;
        }

        // Prevent form submission if there are errors
        if (hasError) {
            event.preventDefault(); // Prevent form from submitting
        }
    });
});
