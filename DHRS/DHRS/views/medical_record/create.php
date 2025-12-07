<?php
/**
 * Create Medical Record View for Doctors
 */

// Set page title
$page_title = 'Create Medical Record';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create Medical Record</h4>
                    <a href="index.php?controller=doctor&action=dashboard" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <form action="index.php?controller=medical_record&action=create" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="citizen_id" class="form-label">Patient <span class="text-danger">*</span></label>
                                <select class="form-select" id="citizen_id" name="citizen_id" required>
                                    <option value="">Select Patient</option>
                                    <?php foreach ($patients as $patient): ?>
                                        <option value="<?php echo $patient['user_id']; ?>" <?php echo (isset($_POST['citizen_id']) && $_POST['citizen_id'] == $patient['user_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name'] . ' (Health ID: ' . $patient['health_id'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="record_type" class="form-label">Record Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="record_type" name="record_type" required>
                                    <option value="">Select Type</option>
                                    <option value="general" <?php echo (isset($_POST['record_type']) && $_POST['record_type'] == 'general') ? 'selected' : ''; ?>>General</option>
                                    <option value="chronic" <?php echo (isset($_POST['record_type']) && $_POST['record_type'] == 'chronic') ? 'selected' : ''; ?>>Chronic</option>
                                    <option value="emergency" <?php echo (isset($_POST['record_type']) && $_POST['record_type'] == 'emergency') ? 'selected' : ''; ?>>Emergency</option>
                                    <option value="vaccination" <?php echo (isset($_POST['record_type']) && $_POST['record_type'] == 'vaccination') ? 'selected' : ''; ?>>Vaccination</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="record_date" class="form-label">Record Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="record_date" name="record_date" value="<?php echo htmlspecialchars($_POST['record_date'] ?? date('Y-m-d')); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="6" required placeholder="Enter detailed medical record information including diagnosis, treatment, medications, and any other relevant details"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=doctor&action=dashboard" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set default date to today
document.getElementById('record_date').value = new Date().toISOString().split('T')[0];

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['citizen_id', 'record_type', 'title', 'description', 'record_date'];
    let isValid = true;
    
    requiredFields.forEach(function(fieldName) {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
