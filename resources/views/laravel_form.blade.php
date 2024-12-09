<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Form</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>

<body>
    <div>
        <div class="contact-form-wrapper d-flex justify-content-center">
            <form id="submitForm" action="{{ url('uploadform') }}" method="POST" enctype="multipart/form-data"
                class="contact-form">
                @csrf
                <h5 class="title">Laravel Form</h5>
                <p class="description">Fill your details to continue!</p>
                <div>
                    <label>Full Name</label>
                    <input type="text" class="form-control rounded border-white mb-3 form-input" id="name"
                        name="name" placeholder="Jon Doe" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" class="form-control rounded border-white mb-3 form-input" id="email"
                        name="email" placeholder="abc@gmail.com" required>
                </div>
                <div>
                    <label>Phone Number</label>
                    <input type="text" class="form-control rounded border-white mb-3 form-input" id="phone"
                        name="phone" placeholder="+91 1234567890" required>
                </div>
                <div>
                    <label>Description</label>
                    <textarea id="message" class="form-control rounded border-white mb-3 form-text-area" id="description"
                        name="description" rows="5" cols="30" placeholder="Description" required></textarea>
                </div>
                <div>
                    <label>Profile Photo</label>
                    <input type="file" class="form-control rounded border-white mb-3 form-input" id="profile_photo"
                        name="profile_photo" required>
                </div>
                <div class="submit-button-wrapper">
                    <input type="submit" value="Submit">
                </div>
            </form>
        </div>

        <!-- Display the data after submission -->
        <div id="data-display" class="mt-4">
            <!-- The submitted data will be shown here in DataTable -->
            <table id="userTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Description</th>
                        <th>Profile Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
       $(document).ready(function() {
    // Initialize DataTable
    var table = $('#userTable').DataTable();

    // Fetch the existing user data from the database when the page loads
    $.ajax({
        url: "{{ url('get-users') }}",
        type: "GET",
        success: function(response) {
            // Add data to DataTable
            response.forEach(function(user) {
                table.row.add([
                    user.name,
                    user.email,
                    user.phone,
                    user.description,
                    `<img src="{{ asset('storage') }}/${user.profile_photo}" alt="Profile Photo" width="100">`
                ]).draw();
            });
        }
    });

    // Handle form submission (AJAX)
    $('#submitForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting normally

        // Clear previous error messages
        $('#error-message').remove();
        $('.is-invalid').removeClass('is-invalid'); // Remove invalid class from fields

        // Initialize validation status
        var valid = true;
        var errorMessages = {
            'name': 'The name field is required.',
            'email': 'The email field is required.',
            'phone': 'Please enter a valid Indian phone number.',
            'description': 'The description field is required.',
            'profile_photo': 'Please upload a valid profile photo (image).'
        };

        // Validate Name
        if ($('#name').val() === '') {
            valid = false;
            $('#name').addClass('is-invalid');
            $('<div class="invalid-feedback">' + errorMessages['name'] + '</div>').insertAfter('#name');
        }

        // Validate Email
        var email = $('#email').val();
        if (email === '') {
            valid = false;
            $('#email').addClass('is-invalid');
            $('<div class="invalid-feedback">The email field is required.</div>').insertAfter('#email');
        } else {
            var emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.(com|org|net|edu|gov|mil|co|in)$/;
            if (!emailRegex.test(email)) {
                valid = false;
                $('#email').addClass('is-invalid');
                $('<div class="invalid-feedback">Please enter a valid email address with a valid domain (e.g., .com, .net, .org, etc.).</div>').insertAfter('#email');
            }
        }

        // Validate Phone
        var phone = $('#phone').val();
        var phoneRegex = /^(?:\+91|91)?[789]\d{9}$/; // Indian phone number regex
        if (!phoneRegex.test(phone)) {
            valid = false;
            $('#phone').addClass('is-invalid');
            $('<div class="invalid-feedback">' + errorMessages['phone'] + '</div>').insertAfter('#phone');
        }

        // Validate Description
        if ($('#description').val() === '') {
            valid = false;
            $('#description').addClass('is-invalid');
            $('<div class="invalid-feedback">' + errorMessages['description'] + '</div>').insertAfter('#description');
        }

        // Validate Profile Photo
        var profilePhoto = $('#profile_photo')[0];
        if (profilePhoto.files.length === 0) {
            valid = false;
            $('#profile_photo').addClass('is-invalid');
            $('<div class="invalid-feedback">' + errorMessages['profile_photo'] + '</div>').insertAfter('#profile_photo');
        } else {
            var file = profilePhoto.files[0];
            var allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                valid = false;
                $('#profile_photo').addClass('is-invalid');
                $('<div class="invalid-feedback">Please upload a valid image file (JPG, PNG, GIF).</div>').insertAfter('#profile_photo');
            }
            if (file.size > 2048000) { // 2MB limit
                valid = false;
                $('#profile_photo').addClass('is-invalid');
                $('<div class="invalid-feedback">The profile photo must not be greater than 2MB.</div>').insertAfter('#profile_photo');
            }
        }

        // If validation fails, show error messages and return
        if (!valid) {
            $('#error-message').remove();
            $('<div id="error-message" class="alert alert-danger">Please correct the errors above.</div>').insertBefore('#submitForm');
            return;
        }

        // If validation passes, submit the form via AJAX
        var formData = new FormData(this);
        $.ajax({
            url: "{{ url('uploadform') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                // Reset the form after submission
                $('#submitForm')[0].reset();

                // Show success message
                $('#success-message').remove();
                $('<div id="success-message" class="alert alert-success">' + result.message + '</div>').insertBefore('#submitForm');

                // Add the submitted data to the DataTable
                var table = $('#userTable').DataTable();
                table.row.add([
                    result.data.name,
                    result.data.email,
                    result.data.phone,
                    result.data.description,
                    `<img src="{{ asset('storage') }}/${result.data.profile_photo}" alt="Profile Photo" width="100">` // Correct URL for image
                ]).draw();

                // Optionally, scroll to the table
                $('html, body').animate({
                    scrollTop: $("#userTable").offset().top
                }, 500);
            },
            error: function() {
                alert('Something went wrong. Please try again!');
            }
        });
    });
});

    </script>
</body>

</html>
