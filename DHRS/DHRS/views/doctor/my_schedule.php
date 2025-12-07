<?php
/**
 * Doctor's Schedule View
 */

// Set page title
$page_title = 'My Schedule';

// Start output buffering
ob_start();

// Define days of the week
$days_of_week = [
    'monday' => 'Monday',
    'tuesday' => 'Tuesday',
    'wednesday' => 'Wednesday',
    'thursday' => 'Thursday',
    'friday' => 'Friday',
    'saturday' => 'Saturday',
    'sunday' => 'Sunday'
];

// Define time slots for dropdown
function generate_time_slots() {
    $slots = [];
    for ($hour = 0; $hour < 24; $hour++) {
        for ($minute = 0; $minute < 60; $minute += 30) {
            $time = sprintf("%02d:%02d", $hour, $minute);
            $display_time = date("h:i A", strtotime($time));
            $slots[$time] = $display_time;
        }
    }
    return $slots;
}

$time_slots = generate_time_slots();

// Define appointment durations
$durations = [
    10 => '10 minutes',
    15 => '15 minutes',
    20 => '20 minutes',
    30 => '30 minutes',
    45 => '45 minutes',
    60 => '1 hour',
    90 => '1.5 hours',
    120 => '2 hours'
];
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title"><i class="fas fa-calendar-alt text-primary me-2"></i> My Schedule</h2>
                <a href="index.php?controller=doctor&action=dashboard" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <!-- Flash messages are now handled by layout.php -->
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Weekly Availability</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Set your weekly schedule to let patients know when you're available for appointments. You can update this anytime.
            </div>
            
            <form action="index.php?controller=doctor&action=update_schedule" method="post" id="scheduleForm">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Day</th>
                                <th width="15%">Available</th>
                                <th width="20%">Start Time</th>
                                <th width="20%">End Time</th>
                                <th width="15%">Max Appointments</th>
                                <th width="15%">Appointment Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($days_of_week as $day_key => $day_name): ?>
                                <tr>
                                    <td class="align-middle">
                                        <strong><?php echo $day_name; ?></strong>
                                    </td>
                                    <td class="align-middle">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input day-available" type="checkbox" 
                                                id="<?php echo $day_key; ?>_available" 
                                                name="<?php echo $day_key; ?>_available" 
                                                data-day="<?php echo $day_key; ?>"
                                                <?php echo (isset($schedules[$day_key]) && $schedules[$day_key]['is_available'] == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $day_key; ?>_available">
                                                Available
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-select time-select" 
                                            id="<?php echo $day_key; ?>_start_time" 
                                            name="<?php echo $day_key; ?>_start_time"
                                            <?php echo (!isset($schedules[$day_key]) || $schedules[$day_key]['is_available'] == 0) ? 'disabled' : ''; ?>>
                                            <option value="">Select Start Time</option>
                                            <?php foreach ($time_slots as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" 
                                                    <?php echo (isset($schedules[$day_key]) && $schedules[$day_key]['start_time'] == $value) ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select time-select" 
                                            id="<?php echo $day_key; ?>_end_time" 
                                            name="<?php echo $day_key; ?>_end_time"
                                            <?php echo (!isset($schedules[$day_key]) || $schedules[$day_key]['is_available'] == 0) ? 'disabled' : ''; ?>>
                                            <option value="">Select End Time</option>
                                            <?php foreach ($time_slots as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" 
                                                    <?php echo (isset($schedules[$day_key]) && $schedules[$day_key]['end_time'] == $value) ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" 
                                            id="<?php echo $day_key; ?>_max_appointments" 
                                            name="<?php echo $day_key; ?>_max_appointments" 
                                            min="1" max="50" 
                                            value="<?php echo (isset($schedules[$day_key])) ? $schedules[$day_key]['max_appointments'] : '10'; ?>"
                                            <?php echo (!isset($schedules[$day_key]) || $schedules[$day_key]['is_available'] == 0) ? 'disabled' : ''; ?>>
                                    </td>
                                    <td>
                                        <select class="form-select" 
                                            id="<?php echo $day_key; ?>_appointment_duration" 
                                            name="<?php echo $day_key; ?>_appointment_duration"
                                            <?php echo (!isset($schedules[$day_key]) || $schedules[$day_key]['is_available'] == 0) ? 'disabled' : ''; ?>>
                                            <?php foreach ($durations as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" 
                                                    <?php echo (isset($schedules[$day_key]) && $schedules[$day_key]['appointment_duration'] == $value) ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Schedule Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">How Scheduling Works</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i> Set your availability for each day of the week</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Define start and end times for your working hours</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Set the maximum number of appointments you can take each day</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i> Choose the duration for each appointment slot</li>
                    </ul>
                    <p class="text-muted">The system will automatically create appointment slots based on your schedule settings.</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Important Notes</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-info-circle text-info me-2"></i> Patients can only book appointments during your available hours</li>
                        <li><i class="fas fa-info-circle text-info me-2"></i> You'll receive notifications for new appointment bookings</li>
                        <li><i class="fas fa-info-circle text-info me-2"></i> You can update your schedule at any time</li>
                        <li><i class="fas fa-info-circle text-info me-2"></i> Existing appointments won't be affected by schedule changes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle availability toggle
        const availabilitySwitches = document.querySelectorAll('.day-available');
        
        availabilitySwitches.forEach(function(switchEl) {
            switchEl.addEventListener('change', function() {
                const day = this.dataset.day;
                const isAvailable = this.checked;
                
                // Enable/disable related fields
                document.getElementById(`${day}_start_time`).disabled = !isAvailable;
                document.getElementById(`${day}_end_time`).disabled = !isAvailable;
                document.getElementById(`${day}_max_appointments`).disabled = !isAvailable;
                document.getElementById(`${day}_appointment_duration`).disabled = !isAvailable;
            });
        });
        
        // Form validation
        const scheduleForm = document.getElementById('scheduleForm');
        
        scheduleForm.addEventListener('submit', function(event) {
            let isValid = true;
            let errorMessage = '';
            
            availabilitySwitches.forEach(function(switchEl) {
                const day = switchEl.dataset.day;
                const isAvailable = switchEl.checked;
                
                if (isAvailable) {
                    const startTime = document.getElementById(`${day}_start_time`).value;
                    const endTime = document.getElementById(`${day}_end_time`).value;
                    
                    if (!startTime || !endTime) {
                        isValid = false;
                        errorMessage = `Please select both start and end times for ${day.charAt(0).toUpperCase() + day.slice(1)}.`;
                    } else if (startTime >= endTime) {
                        isValid = false;
                        errorMessage = `End time must be after start time for ${day.charAt(0).toUpperCase() + day.slice(1)}.`;
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert(errorMessage);
            }
        });
    });
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
