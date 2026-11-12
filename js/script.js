// jQuery is used for the live preview and small form interactions.
$(function () {
    const $form = $('#registrationForm');
    if (!$form.length) return;

    function updatePreview() {
        const name = $('#full_name').val().trim();
        const email = $('#email').val().trim();
        const course = $('#course').val();
        const year = $('#year').val();

        $('#previewName').text(name || '—');
        $('#previewEmail').text(email || '—');
        $('#previewCourse').text(course || '—');
        $('#previewYear').text(year || '—');
    }

    $form.on('input change', 'input, select, textarea', updatePreview);
    updatePreview();

    function setError(fieldName, message) {
        const $field = $('[name="' + fieldName + '"]').closest('.field');
        $field.toggleClass('invalid', Boolean(message));
        $('[data-error-for="' + fieldName + '"]').text(message || '');
    }

    $form.on('submit', function (event) {
        let valid = true;
        const name = $('#full_name').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const address = $('#address').val().trim();

        const checks = [
            ['full_name', name.length >= 3 ? '' : 'Please enter at least 3 characters.'],
            ['email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? '' : 'Please enter a valid email.'],
            ['phone', /^[0-9+()\-\s]{10,16}$/.test(phone) ? '' : 'Please enter a valid phone number.'],
            ['dob', $('#dob').val() ? '' : 'Please select your date of birth.'],
            ['gender', $('#gender').val() ? '' : 'Please select an option.'],
            ['course', $('#course').val() ? '' : 'Please select a course.'],
            ['year', $('#year').val() ? '' : 'Please select your current year.'],
            ['address', address.length >= 8 ? '' : 'Please enter at least 8 characters.']
        ];

        checks.forEach(function (check) {
            setError(check[0], check[1]);
            if (check[1]) valid = false;
        });

        if (!$('[name="declaration"]').is(':checked')) {
            valid = false;
            window.alert('Please confirm the declaration before submitting.');
        }

        if (!valid) {
            event.preventDefault();
            const $firstInvalid = $('.field.invalid').first();
            if ($firstInvalid.length) {
                $('html, body').animate({ scrollTop: $firstInvalid.offset().top - 90 }, 250);
            }
        }
    });

    $('#address').on('input', function () {
        $('#addressCount').text($(this).val().length + ' / 250');
    }).trigger('input');
});
