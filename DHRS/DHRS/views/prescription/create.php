<?php
/**
 * Create Prescription Form
 * 
 * This view allows doctors to create prescriptions for patients.
 */

// Include the layout
include('views/layout.php');
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-prescription"></i> Create New Prescription
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Patient Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Patient Information</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['citizen_name']); ?></p>
                            <p><strong>Health ID:</strong> <?php echo htmlspecialchars($appointment['health_id']); ?></p>
                            <p><strong>Gender:</strong> <?php echo ucfirst($appointment['gender']); ?></p>
                            <p><strong>Date of Birth:</strong> <?php echo format_date($appointment['date_of_birth']); ?></p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">Appointment Details</h6>
                            <p><strong>Date:</strong> <?php echo format_date($appointment['appointment_date']); ?></p>
                            <p><strong>Time:</strong> <?php echo format_time($appointment['appointment_time']); ?></p>
                            <p><strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
                            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($appointment['specialization']); ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <form action="index.php?controller=prescription&action=process_create" method="POST" id="prescriptionForm">
                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                        
                        <div class="form-group mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis *</label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" 
                                      placeholder="Enter the diagnosis..." required></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="instructions" class="form-label">General Instructions *</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="3" 
                                      placeholder="Enter general instructions for the patient..." required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date (Optional)</label>
                                    <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" 
                                           min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" 
                                              placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Medications Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted mb-0">Medications *</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addMedication()">
                                    <i class="fas fa-plus"></i> Add Medication
                                </button>
                            </div>
                            
                            <div id="medicationsContainer">
                                <!-- Medications will be added here dynamically -->
                            </div>
                            
                            <div class="text-center" id="noMedications">
                                <p class="text-muted">No medications added yet. Click "Add Medication" to start.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php?controller=doctor&action=dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let medicationCount = 0;

function addMedication() {
    medicationCount++;
    
    const container = document.getElementById('medicationsContainer');
    const noMedications = document.getElementById('noMedications');
    
    // Hide the "no medications" message
    noMedications.style.display = 'none';
    
    const medicationDiv = document.createElement('div');
    medicationDiv.className = 'card mb-3';
    medicationDiv.id = `medication_${medicationCount}`;
    
    medicationDiv.innerHTML = `
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Medication ${medicationCount}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeMedication(${medicationCount})">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Medication Name *</label>
                        <input type="text" class="form-control" name="medications[${medicationCount}][name]" 
                               placeholder="e.g., Paracetamol" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Dosage *</label>
                        <input type="text" class="form-control" name="medications[${medicationCount}][dosage]" 
                               placeholder="e.g., 500mg" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Frequency *</label>
                        <input type="text" class="form-control" name="medications[${medicationCount}][frequency]" 
                               placeholder="e.g., Twice daily" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Duration *</label>
                        <input type="text" class="form-control" name="medications[${medicationCount}][duration]" 
                               placeholder="e.g., 7 days" required>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Special Instructions (Optional)</label>
                <textarea class="form-control" name="medications[${medicationCount}][special_instructions]" 
                          rows="2" placeholder="Any special instructions..."></textarea>
            </div>
        </div>
    `;
    
    container.appendChild(medicationDiv);
}

function removeMedication(id) {
    const medicationDiv = document.getElementById(`medication_${id}`);
    medicationDiv.remove();
    
    // Check if there are any medications left
    const container = document.getElementById('medicationsContainer');
    const noMedications = document.getElementById('noMedications');
    
    if (container.children.length === 0) {
        noMedications.style.display = 'block';
    }
}

// Form validation
document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
    const medications = document.querySelectorAll('[name^="medications"][name$="[name]"]');
    
    if (medications.length === 0) {
        e.preventDefault();
        alert('Please add at least one medication to the prescription.');
        return false;
    }
    
    // Validate each medication
    let isValid = true;
    medications.forEach(function(medication) {
        if (!medication.value.trim()) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required medication fields.');
        return false;
    }
});

// Add first medication automatically
document.addEventListener('DOMContentLoaded', function() {
    addMedication();
});
</script>

<?php
// Include the footer
include('views/includes/layout_footer.php');
?>
