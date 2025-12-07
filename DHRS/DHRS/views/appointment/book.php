<?php
/**
 * Appointment Booking Form
 * 
 * This view allows citizens to book appointments with doctors.
 */

// Set page title
$page_title = 'Book Appointment';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Header Section -->
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Book New Appointment
                </h2>
                <p class="text-muted">Schedule your appointment with our healthcare professionals</p>
            </div>

            <!-- Main Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <form action="index.php?controller=appointment&action=process_booking" method="POST" id="appointmentForm" class="needs-validation" novalidate>
                        
                        <!-- Step 1: Doctor Selection -->
                        <div class="step-section mb-4">
                            <h5 class="step-title mb-3">
                                <span class="step-number">1</span>
                                Select Doctor
                            </h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="doctor_id" name="doctor_id" required>
                                            <option value="">Choose a doctor...</option>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <option value="<?php echo $doctor['id']; ?>" 
                                                        data-specialization="<?php echo htmlspecialchars($doctor['specialization']); ?>"
                                                        data-fee="<?php echo $doctor['consultation_fee']; ?>"
                                                        data-experience="<?php echo $doctor['experience_years']; ?>"
                                                        data-qualification="<?php echo htmlspecialchars($doctor['qualification']); ?>">
                                                    Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                                                    - <?php echo htmlspecialchars($doctor['specialization']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="doctor_id">Select Doctor *</label>
                                        <div class="invalid-feedback">Please select a doctor.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="consultation_type" name="consultation_type">
                                            <option value="in-person">In-Person</option>
                                            <option value="telemedicine" selected>Telemedicine</option>
                                            <option value="home-visit">Home Visit</option>
                                        </select>
                                        <label for="consultation_type">Consultation Type</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Doctor Information Card -->
                            <div id="doctorInfo" class="card bg-light border-0" style="display: none;">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1 text-primary">
                                                <i class="fas fa-user-md me-2"></i>
                                                <span id="doctorName">Doctor Name</span>
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-stethoscope me-1"></i>
                                                <span id="doctorSpecialization">Specialization</span>
                                            </p>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                <span id="doctorQualification">Qualification</span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="text-success">
                                                <strong id="doctorFee">Consultation Fee</strong>
                                            </div>
                                            <small class="text-muted" id="doctorExperience">Experience</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Date & Time Selection -->
                        <div class="step-section mb-4">
                            <h5 class="step-title mb-3">
                                <span class="step-number">2</span>
                                Select Date & Time
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                               min="<?php echo date('Y-m-d'); ?>" 
                                               max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                                        <label for="appointment_date">Appointment Date *</label>
                                        <div class="invalid-feedback">Please select a valid date.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="appointment_time" name="appointment_time" required>
                                            <option value="">Select time...</option>
                                            <!-- Time slots will be populated dynamically -->
                                        </select>
                                        <label for="appointment_time">Preferred Time *</label>
                                        <div class="invalid-feedback">Please select a time slot.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Time Slots Info -->
                            <div id="timeSlotsInfo" class="alert alert-info" style="display: none;">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="timeSlotsText">Available time slots will be shown after selecting a doctor and date.</span>
                            </div>
                        </div>

                        <!-- Step 3: Appointment Details -->
                        <div class="step-section mb-4">
                            <h5 class="step-title mb-3">
                                <span class="step-number">3</span>
                                Appointment Details
                            </h5>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="reason" name="reason" rows="4" 
                                          placeholder="Please describe your symptoms or reason for the appointment..." 
                                          style="height: 120px;" required></textarea>
                                <label for="reason">Reason for Visit *</label>
                                <div class="invalid-feedback">Please provide a reason for your visit.</div>
                                <div class="form-text">Please be specific about your symptoms or concerns to help the doctor prepare for your appointment.</div>
                            </div>
                        </div>
                        
                        <!-- Existing Appointments Warning -->
                        <?php if (!empty($existing_appointments)): ?>
                            <div class="alert alert-warning border-0 mb-4">
                                <h6 class="mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Existing Appointments
                                </h6>
                                <p class="mb-2">You have the following appointments scheduled:</p>
                                <ul class="mb-0">
                                    <?php foreach ($existing_appointments as $appointment): ?>
                                        <li>
                                            <strong><?php echo format_date($appointment['appointment_date']); ?></strong> 
                                            at <?php echo format_time($appointment['appointment_time']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="index.php?controller=citizen&action=dashboard" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-calendar-check me-2"></i> Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for the appointment form -->
<style>
.step-section {
    position: relative;
}

.step-title {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-radius: 50%;
    font-size: 14px;
    font-weight: bold;
    margin-right: 12px;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #007bff;
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

#doctorInfo {
    border-radius: 8px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.alert {
    border-radius: 8px;
}

@media (max-width: 768px) {
    .step-title {
        font-size: 1rem;
    }
    
    .step-number {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const doctorInfo = document.getElementById('doctorInfo');
    const appointmentDate = document.getElementById('appointment_date');
    const appointmentTime = document.getElementById('appointment_time');
    const timeSlotsInfo = document.getElementById('timeSlotsInfo');
    const timeSlotsText = document.getElementById('timeSlotsText');
    const form = document.getElementById('appointmentForm');
    
    // Doctor selection handler
    doctorSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const name = selectedOption.textContent.split(' - ')[0];
            const specialization = selectedOption.dataset.specialization;
            const fee = selectedOption.dataset.fee;
            const experience = selectedOption.dataset.experience;
            const qualification = selectedOption.dataset.qualification;
            
            // Update doctor info display
            document.getElementById('doctorName').textContent = name;
            document.getElementById('doctorSpecialization').textContent = specialization;
            document.getElementById('doctorQualification').textContent = qualification;
            document.getElementById('doctorFee').textContent = fee ? `₹${fee}` : 'Not specified';
            document.getElementById('doctorExperience').textContent = `${experience} years experience`;
            
            doctorInfo.style.display = 'block';
            
            // Load time slots if date is selected
            if (appointmentDate.value) {
                loadTimeSlots();
            }
        } else {
            doctorInfo.style.display = 'none';
            appointmentTime.innerHTML = '<option value="">Select time...</option>';
            timeSlotsInfo.style.display = 'none';
        }
    });
    
    // Date selection handler
    appointmentDate.addEventListener('change', function() {
        if (this.value && doctorSelect.value) {
            loadTimeSlots();
        } else if (this.value) {
            timeSlotsText.textContent = 'Please select a doctor first to see available time slots.';
            timeSlotsInfo.style.display = 'block';
        }
    });
    
    // Load available time slots
    function loadTimeSlots() {
        if (!doctorSelect.value || !appointmentDate.value) return;
        
        // Clear existing options
        appointmentTime.innerHTML = '<option value="">Loading time slots...</option>';
        
        // Generate time slots (9 AM to 5:30 PM, 30-minute intervals)
        const timeSlots = [];
        const startHour = 9;
        const endHour = 17;
        
        for (let hour = startHour; hour <= endHour; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                if (hour === endHour && minute > 0) break; // Stop at 5:30 PM
                
                const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}:00`;
                const displayTime = formatTime(timeString);
                timeSlots.push({ value: timeString, display: displayTime });
            }
        }
        
        // Populate time slots
        appointmentTime.innerHTML = '<option value="">Select time...</option>';
        timeSlots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot.value;
            option.textContent = slot.display;
            appointmentTime.appendChild(option);
        });
        
        // Show info
        timeSlotsText.textContent = `Available time slots for ${formatDate(appointmentDate.value)}. Please select your preferred time.`;
        timeSlotsInfo.style.display = 'block';
    }
    
    // Format time for display
    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }
    
    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Remove previous validation classes
        form.classList.remove('was-validated');
        
        // Check required fields
        const doctor = doctorSelect.value;
        const date = appointmentDate.value;
        const time = appointmentTime.value;
        const reason = document.getElementById('reason').value.trim();
        
        let isValid = true;
        
        if (!doctor) {
            doctorSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            doctorSelect.classList.remove('is-invalid');
        }
        
        if (!date) {
            appointmentDate.classList.add('is-invalid');
            isValid = false;
        } else {
            appointmentDate.classList.remove('is-invalid');
        }
        
        if (!time) {
            appointmentTime.classList.add('is-invalid');
            isValid = false;
        } else {
            appointmentTime.classList.remove('is-invalid');
        }
        
        if (!reason) {
            document.getElementById('reason').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('reason').classList.remove('is-invalid');
        }
        
        // Check date validity
        if (date) {
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                appointmentDate.classList.add('is-invalid');
                isValid = false;
                alert('Appointment date cannot be in the past.');
            }
        }
        
        if (isValid) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Booking...';
            submitBtn.disabled = true;
            
            // Submit form
            setTimeout(() => {
                form.submit();
            }, 500);
        } else {
            form.classList.add('was-validated');
        }
    });
    
    // Real-time validation
    [doctorSelect, appointmentDate, appointmentTime, document.getElementById('reason')].forEach(element => {
        element.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layout.php');
?>
