<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale CRUD</title>
    
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
                <a class="nav-link active" href="/">Home</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="/customer">Customer</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Sale CRUD</h2>
        
        <button type="button" class="btn btn-primary bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-3"  data-toggle="modal" data-target="#saleModal" onclick="enabledAmount()">
            Add Sale
        </button>
        

        
        <table class="table mt-3" id="saleTable">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Customer ID</th>
                    <th>Sale Date</th>
                    <th>Total Amount</th>
                    <th>Product Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    
    <div class="modal" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleModalLabel">Add Sale</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <form id="saleForm">
                        
                        <input type="hidden" id="saleId" name="id">
                        <div class="form-group">
                            <label for="customers_id">Customer ID</label>
                            <input type="text" class="form-control" id="customers_id" name="customers_id">
                            <div id="customers_idErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="sale_date">Sale Date</label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date">
                            <div id="sale_dateErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="total_amount">Total Amount</label>
                            <input type="total_amount" class="form-control" id="total_amount" name="total_amount">
                            <div id="total_amountErrors" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                            <label for="name_product">Name Product</label>
                            <input type="text" class="form-control" id="name_product" name="name_product">
                            <div id="name_productErrors" class="text-danger"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Sale</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    
    <script>
        var onEdit = false;
        function enabledAmount() {
            onEdit = false;
            $('#saleForm input').val(''); 
            // $('#total_amount').prop('disabled', false)
        }
        // $('#total_amount').prop('disabled', false);
        $(document).ready(function () {
            // Function to fetch and display sale data
            function fetchSales() {
                $.ajax({
                    url: 'http://localhost:8000/api/sale',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        // Clear existing table rows
                        $('#saleTable tbody').empty();

                        // Populate table with sale data
                        $.each(data.data, function (index, sale) {
                            var row = `<tr>
                                <td>${sale.id}</td>
                                <td>${sale.customers_id}</td>
                                <td>${sale.sale_date}</td>
                                <td>${sale.total_amount}</td>
                                <td>${sale.name_product}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editSale(${sale.id})">Edit</button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="deleteSale(${sale.id})">Delete</button>
                                </td>
                            </tr>`;
                            $('#saleTable tbody').append(row);
                        });
                    },
                    error: function (error) {
                        console.error('Error fetching sales:', error);
                    },
                });
            }

            $('#saleForm').submit(function (event) {
                event.preventDefault();

                var formData = $(this).serialize();

                if (onEdit) {
                    // If in edit mode, update the sale
                    var saleId = $('#saleId').val();
                    updateSale(saleId, formData);
                } else {
                    // Otherwise, add a new sale
                    addSale(formData);
                }
            });

            // Function to handle form submission for adding a new sale
            function addSale(formData) {
                $.ajax({
                    url: 'http://localhost:8000/api/sale',
                    method: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function () {
                        // Clear validation errors
                        clearValidationErrors();

                        // Refresh sale data after submission
                        fetchSales();
                        $('#saleModal').modal('hide');
                    },
                    error: function (error) {
                        handleErrors(error);
                    },
                });
            }

            // Function to handle form submission for updating an existing sale
            function updateSale(saleId, formData) {
                $.ajax({
                    url: `http://localhost:8000/api/sale/${saleId}`,
                    method: 'PUT',
                    dataType: 'json',
                    data: formData,
                    success: function () {
                        // Clear validation errors
                        clearValidationErrors();

                        // Refresh sale data after submission
                        fetchSales();
                        $('#saleModal').modal('hide');
                    },
                    error: function (error) {
                        handleErrors(error);
                    },
                });
            }

            // Function to clear validation errors
            function clearValidationErrors() {
                // Loop through each form group and clear validation errors
                $('#saleForm .form-group').each(function () {
                    var errorElement = $(this).find('.text-danger');
                    if (errorElement.length) {
                        errorElement.html('');
                    }
                });
            }

            // Function to handle errors
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
                    // Check if the error response contains an "Insufficient balance" error
                    if (error.responseJSON && error.responseJSON.error && error.responseJSON.error === 'Insufficient balance.') {
                        // Display the insufficient balance error to the user
                        $('#total_amountErrors').html('Insufficient balance.');
                    } else {
                        console.error('Error saving sale:', error);
                    }
                }
            }


            // Function to edit a sale
            window.editSale = function (saleId) {
                onEdit = true;
                // Fetch sale data by ID
                $.ajax({
                    url: `http://localhost:8000/api/sale/${saleId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (sale) {
                        // Populate form with sale data
                        $('#saleId').val(sale.data.id); // Store the sale ID
                        $('#customers_id').val(sale.data.customers_id);
                        $('#sale_date').val(sale.data.sale_date);
                        $('#total_amount').val(sale.data.total_amount);
                        $('#name_product').val(sale.data.name_product);
                        $('#address').val(sale.data.address);

                        // $('#total_amount').prop('disabled', true);

                        // Show the modal
                        $('#saleModal').modal('show');
                    },
                    error: function (error) {
                        console.error('Error fetching sale details:', error);
                    },
                });
            };

            // Function to delete a sale
            window.deleteSale = function (saleId) {
                if (confirm('Are you sure you want to delete this sale?')) {
                    $.ajax({
                        url: `http://localhost:8000/api/sale/${saleId}`,
                        method: 'DELETE',
                        success: function () {
                            // Refresh sale data after deletion
                            fetchSales();
                        },
                        error: function (error) {
                            console.error('Error deleting sale:', error);
                        },
                    });
                }
            };

            // Fetch initial sale data when the page loads
            fetchSales();
        });
    </script>

</body>

</html>
<?php /**PATH /Users/qindexmedia/Downloads/project-surabaya/customer-penjualan/resources/views/sale.blade.php ENDPATH**/ ?>