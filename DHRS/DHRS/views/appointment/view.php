<?php
/**
 * Appointment View Page
 * 
 * This view displays appointment details and allows users to perform actions.
 */

// Include the layout
include('views/layout.php');
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-check"></i> Appointment Details
                        </h4>
                        <span class="badge bg-<?php echo get_status_color($appointment['status']); ?> fs-6">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Patient Information</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['citizen_name']); ?></p>
                            <p><strong>Health ID:</strong> <?php echo htmlspecialchars($appointment['health_id']); ?></p>
                            <p><strong>Gender:</strong> <?php echo ucfirst($appointment['gender']); ?></p>
                            <p><strong>Date of Birth:</strong> <?php echo format_date($appointment['date_of_birth']); ?></p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">Doctor Information</h6>
                            <p><strong>Name:</strong> Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
                            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($appointment['specialization']); ?></p>
                            <p><strong>Consultation Fee:</strong> $<?php echo $appointment['consultation_fee'] ?: 'Not specified'; ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Appointment Details</h6>
                            <p><strong>Date:</strong> <?php echo format_date($appointment['appointment_date']); ?></p>
                            <p><strong>Time:</strong> <?php echo format_time($appointment['appointment_time']); ?></p>
                            <p><strong>Type:</strong> <?php echo ucfirst(str_replace('-', ' ', $appointment['consultation_type'])); ?></p>
                            <p><strong>Reason:</strong> <?php echo htmlspecialchars($appointment['reason']); ?></p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">Status Information</h6>
                            <p><strong>Status:</strong> <?php echo ucfirst($appointment['status']); ?></p>
                            <p><strong>Booked On:</strong> <?php echo format_date($appointment['created_at']); ?></p>
                            
                            <?php if ($appointment['status'] === 'cancelled'): ?>
                                <p><strong>Cancelled On:</strong> <?php echo format_date($appointment['cancelled_at']); ?></p>
                                <?php if ($appointment['cancellation_reason']): ?>
                                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($appointment['cancellation_reason']); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($appointment['status'] === 'completed'): ?>
                                <p><strong>Completed On:</strong> <?php echo format_date($appointment['completed_at']); ?></p>
                                <?php if ($appointment['diagnosis']): ?>
                                    <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($appointment['diagnosis']); ?></p>
                                <?php endif; ?>
                                <?php if ($appointment['next_visit_date']): ?>
                                    <p><strong>Next Visit:</strong> <?php echo format_date($appointment['next_visit_date']); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($appointment['status'] === 'completed' && $appointment['treatment_notes']): ?>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted">Treatment Notes</h6>
                                <div class="bg-light p-3 rounded">
                                    <?php echo nl2br(htmlspecialchars($appointment['treatment_notes'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <?php if ($_SESSION['role'] === 'citizen'): ?>
                            <a href="index.php?controller=citizen&action=dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            
                            <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times"></i> Cancel Appointment
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['role'] === 'doctor'): ?>
                            <a href="index.php?controller=doctor&action=dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                            
                            <?php if ($appointment['status'] === 'confirmed'): ?>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                                    <i class="fas fa-check"></i> Complete Appointment
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($appointment['status'] === 'pending'): ?>
                                <button type="button" class="btn btn-primary" onclick="confirmAppointment(<?php echo $appointment['id']; ?>)">
                                    <i class="fas fa-check-circle"></i> Confirm Appointment
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<?php if ($_SESSION['role'] === 'citizen' && ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed')): ?>
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?controller=appointment&action=cancel" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                    <p>Are you sure you want to cancel this appointment?</p>
                    <div class="form-group">
                        <label for="reason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Please provide a reason for cancellation..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Complete Appointment Modal -->
<?php if ($_SESSION['role'] === 'doctor' && $appointment['status'] === 'confirmed'): ?>
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?controller=appointment&action=complete" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="diagnosis" class="form-label">Diagnosis</label>
                                <textarea class="form-control" name="diagnosis" rows="3" placeholder="Enter diagnosis..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="next_visit_date" class="form-label">Next Visit Date (Optional)</label>
                                <input type="date" class="form-control" name="next_visit_date" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="treatment_notes" class="form-label">Treatment Notes</label>
                        <textarea class="form-control" name="treatment_notes" rows="4" placeholder="Enter treatment notes, medications prescribed, and follow-up instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function confirmAppointment(appointmentId) {
    if (confirm('Are you sure you want to confirm this appointment?')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=appointment&action=confirm';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'appointment_id';
        input.value = appointmentId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
// Helper function to get status color
function get_status_color($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'confirmed':
            return 'primary';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Include the footer
include('views/includes/layout_footer.php');
?>
