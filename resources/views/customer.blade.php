<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer CRUD</title>

   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</head>

<body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="/customer">Customer</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-2xl font-bold mb-2 mt-10">Customer CRUD</h2>

        <button type="button"
            class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-3"
            data-toggle="modal" data-target="#customerModal">
            Add Customer
        </button>

        <table class="table mt-3" id="customerTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Emoney Balance</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <div class="modal" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="customerForm">

                        <input type="hidden" id="customerId" name="id">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name">
                            <div id="first_nameErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name">
                            <div id="last_nameErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <div id="emailErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="emoney">Emoney</label>
                            <input type="emoney" class="form-control" id="emoney" name="emoney">
                            <div id="emoneyErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number">
                            <div id="phone_numberErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            <div id="addressErrors" class="text-danger"></div>
                        </div>
                        <button type="submit"
                            class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Customer
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    
    <script>
        var onEdit = false;
        $(document).ready(function () {
            // Function to fetch and display customer data
            function fetchCustomers() {
                $.ajax({
                    url: 'http://localhost:8000/api/customer',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        // Clear existing table rows
                        $('#customerTable tbody').empty();

                        // Populate table with customer data
                        $.each(data.data, function (index, customer) {
                            var row = `<tr>
                                <td>${customer.id}</td>
                                <td>${customer.first_name}</td>
                                <td>${customer.last_name}</td>
                                <td>${customer.email}</td>
                                <td>${customer.emoney}</td>
                                <td>${customer.phone_number}</td>
                                <td>${customer.address}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editCustomer(${customer.id})">Edit</button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="deleteCustomer(${customer.id})">Delete</button>
                                </td>
                            </tr>`;
                            $('#customerTable tbody').append(row);
                        });
                    },
                    error: function (error) {
                        console.error('Error fetching customers:', error);
                    },
                });
            }

            $('#customerForm').submit(function (event) {
                event.preventDefault();

                var formData = $(this).serialize();

                if (onEdit) {
                    // If in edit mode, update the customer
                    var customerId = $('#customerId').val();
                    updateCustomer(customerId, formData);
                } else {
                    // Otherwise, add a new customer
                    addCustomer(formData);
                }
            });

            // Function to handle form submission for adding a new customer
            function addCustomer(formData) {
                $.ajax({
                    url: 'http://localhost:8000/api/customer',
                    method: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function () {
                        // Clear validation errors
                        clearValidationErrors();

                        // Refresh customer data after submission
                        fetchCustomers();
                        $('#customerModal').modal('hide');
                    },
                    error: function (error) {
                        handleErrors(error);
                    },
                });
            }

            // Function to handle form submission for updating an existing customer
            function updateCustomer(customerId, formData) {
                $.ajax({
                    url: `http://localhost:8000/api/customer/${customerId}`,
                    method: 'PUT',
                    dataType: 'json',
                    data: formData,
                    success: function () {
                        // Clear validation errors
                        clearValidationErrors();

                        // Refresh customer data after submission
                        fetchCustomers();
                        $('#customerModal').modal('hide');
                    },
                    error: function (error) {
                        handleErrors(error);
                    },
                });
            }

            // Function to clear validation errors
            function clearValidationErrors() {
                // Loop through each form group and clear validation errors
                $('#customerForm .form-group').each(function () {
                    var errorElement = $(this).find('.text-danger');
                    if (errorElement.length) {
                        errorElement.html('');
                    }
                });
            }

            // Function to handle errors
            function handleErrors(error) {
                // Check if the error response contains validation errors
                if (error.responseJSON && error.responseJSON.errors) {
                    var validationErrors = error.responseJSON.errors;

                    // Display validation errors to the user
                    $.each(validationErrors, function (field, errors) {
                        // Check if the corresponding element exists before updating its content
                        var errorElement = $(`#${field}Errors`);
                        if (errorElement.length) {
                            errorElement.html(errors.join('<br>'));
                        } else {
                            console.error(`Element with ID ${field}Errors not found.`);
                        }
                    });
                } else {
                    console.error('Error saving customer:', error);
                }
            }

            // Function to edit a customer
            window.editCustomer = function (customerId) {
                onEdit = true;
                // Fetch customer data by ID
                $.ajax({
                    url: `http://localhost:8000/api/customer/${customerId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (customer) {
                        // Populate form with customer data
                        $('#customerId').val(customer.data.id); // Store the customer ID
                        $('#first_name').val(customer.data.first_name);
                        $('#last_name').val(customer.data.last_name);
                        $('#email').val(customer.data.email);
                         $('#emoney').val(customer.data.emoney);
                        $('#phone_number').val(customer.data.phone_number);
                        $('#address').val(customer.data.address);

                        // Show the modal
                        $('#customerModal').modal('show');
                    },
                    error: function (error) {
                        console.error('Error fetching customer details:', error);
                    },
                });
            };

            // Function to delete a customer
            window.deleteCustomer = function (customerId) {
                if (confirm('Are you sure you want to delete this customer?')) {
                    $.ajax({
                        url: `http://localhost:8000/api/customer/${customerId}`,
                        method: 'DELETE',
                        success: function () {
                            // Refresh customer data after deletion
                            fetchCustomers();
                        },
                        error: function (error) {
                            console.error('Error deleting customer:', error);
                        },
                    });
                }
            };

            // Fetch initial customer data when the page loads
            fetchCustomers();
        });
    </script>

</body>

</html>
