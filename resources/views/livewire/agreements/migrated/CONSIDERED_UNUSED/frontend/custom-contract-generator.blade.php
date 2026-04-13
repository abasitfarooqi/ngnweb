<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Custom Contract Generator - Neguinho Motors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 1400px;
        }
        
        .header-section {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .form-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .section-title {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc3545;
        }
        
        .form-control, .form-select {
            border: 2px solid #dee2e6;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .btn-generate {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 15px 40px;
            font-weight: bold;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(40, 167, 69, 0.3);
        }
        
        .signature-upload {
            border: 3px dashed #dee2e6;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .signature-upload:hover {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }
        
        .signature-preview {
            max-width: 200px;
            max-height: 100px;
            margin: 10px auto;
            display: block;
        }
        
        .contract-type-card {
            border: 2px solid #dee2e6;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .contract-type-card:hover {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }
        
        .contract-type-card.selected {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #dc3545;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @@keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert-custom {
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-3">
        <div class="main-container">
            <div class="header-section">
                <h1><i class="fas fa-file-contract me-3"></i>Custom Contract Generator</h1>
                <p class="mb-0">Generate Motorcycle Hire/Sale Contracts with Custom Data</p>
            </div>
            
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form id="contractGeneratorForm" enctype="multipart/form-data">
                    <!-- Contract Type Selection -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-clipboard-list me-2"></i>Contract Type</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="contract-type-card selected" data-type="regular">
                                    <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                                    <h5>Regular Contract</h5>
                                    <p class="text-muted">Standard 5-month contract</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="contract-type-card" data-type="used">
                                    <i class="fas fa-motorcycle fa-3x text-warning mb-3"></i>
                                    <h5>Used Vehicle</h5>
                                    <p class="text-muted">Contract for used motorcycles</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="contract-type-card" data-type="custom">
                                    <i class="fas fa-cogs fa-3x text-success mb-3"></i>
                                    <h5>Custom Extended</h5>
                                    <p class="text-muted">18-month custom contract</p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="contractType" name="contract_type" value="regular">
                    </div>

                    <!-- Customer Information -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-user me-2"></i>Customer Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="tel" class="form-control" name="whatsapp" placeholder="Optional">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Address *</label>
                                    <textarea class="form-control" name="address" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" class="form-control" name="city" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Postcode *</label>
                                    <input type="text" class="form-control" name="postcode" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- License Information -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-id-card me-2"></i>License Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">License Number *</label>
                                    <input type="text" class="form-control" name="license_number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Issuance Authority *</label>
                                    <input type="text" class="form-control" name="license_authority" value="United Kingdom" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Issuance Date *</label>
                                    <input type="date" class="form-control" name="license_issuance_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiry Date *</label>
                                    <input type="date" class="form-control" name="license_expiry_date" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vehicle Information -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-motorcycle me-2"></i>Vehicle Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Registration Number *</label>
                                    <input type="text" class="form-control" name="reg_no" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Type *</label>
                                    <input type="text" class="form-control" name="type_approval" value="Motorcycle" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Make *</label>
                                    <input type="text" class="form-control" name="make" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Model *</label>
                                    <input type="text" class="form-control" name="model" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Engine *</label>
                                    <input type="text" class="form-control" name="engine" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Color *</label>
                                    <input type="text" class="form-control" name="color" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Information -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-calendar me-2"></i>Contract Information</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Contract ID *</label>
                                    <input type="number" class="form-control" name="booking_id" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Contract Start Date *</label>
                                    <input type="datetime-local" class="form-control" name="contract_date" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Price (£) *</label>
                                    <input type="number" class="form-control" name="vehicle_price" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Deposit Paid (£) *</label>
                                    <input type="number" class="form-control" name="deposit" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Payment Type *</label>
                                    <select class="form-select" name="payment_type" required>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Installment Amount (£) *</label>
                                    <input type="number" class="form-control" name="installment" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Additional Accessories</label>
                                    <input type="text" class="form-control" name="extra_items" placeholder="Optional">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Accessories Total (£)</label>
                                    <input type="number" class="form-control" name="extra_cost" step="0.01" value="0">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Staff Member *</label>
                                    <input type="text" class="form-control" name="staff_name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Signature Information -->
                    <div class="form-section">
                        <h4 class="section-title"><i class="fas fa-signature me-2"></i>Signature Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Custom Signing Date *</label>
                                    <input type="date" class="form-control" name="signing_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Upload Signature (PNG/JPG) *</label>
                                    <input type="file" class="form-control" name="signature_file" accept=".png,.jpg,.jpeg" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="signature-upload" id="signaturePreview">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Signature preview will appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-generate btn-lg">
                            <i class="fas fa-file-pdf me-2"></i>Generate Contract PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center text-white">
            <div class="spinner mb-3"></div>
            <h4>Generating Contract...</h4>
            <p>Please wait while we create your custom contract</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Contract type selection
        document.querySelectorAll('.contract-type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.contract-type-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('contractType').value = this.dataset.type;
            });
        });

        // Signature preview
        document.querySelector('input[name="signature_file"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('signaturePreview');
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="signature-preview" alt="Signature Preview">
                        <p class="text-success mt-2"><i class="fas fa-check me-2"></i>Signature uploaded successfully</p>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        // Auto-set current date/time for contract date
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const offset = now.getTimezoneOffset() * 60000;
            const localDate = new Date(now.getTime() - offset);
            document.querySelector('input[name="contract_date"]').value = localDate.toISOString().slice(0, 16);
            document.querySelector('input[name="signing_date"]').value = localDate.toISOString().slice(0, 10);
        });

        // Form submission
        document.getElementById('contractGeneratorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            const messageContainer = document.getElementById('messageContainer');
            
            // Show loading
            loadingOverlay.style.display = 'flex';
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Add CSRF token
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Make actual API call to Laravel backend
            fetch('/generate-custom-contract', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/pdf')) {
                        return response.blob();
                    } else if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Failed to generate PDF');
                        });
                    } else {
                        return response.blob();
                    }
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .then(blob => {
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'contract.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                loadingOverlay.style.display = 'none';
                
                // Show success message
                messageContainer.innerHTML = `
                    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1056">
                        <div class="alert alert-success alert-custom alert-dismissible" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> Contract generated and downloaded successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                `;
                
                // Auto-hide success message after 5 seconds
                setTimeout(() => {
                    const alert = messageContainer.querySelector('.alert');
                    if (alert) alert.remove();
                }, 5000);
            })
            .catch(error => {
                loadingOverlay.style.display = 'none';
                console.error('Error:', error);
                
                // Show error message
                messageContainer.innerHTML = `
                    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1056">
                        <div class="alert alert-danger alert-custom alert-dismissible" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Error!</strong> Failed to generate contracts. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>