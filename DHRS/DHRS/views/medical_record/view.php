<?php
/**
 * Medical Record Detail View
 */

// Set page title
$page_title = 'Medical Record Details';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Record Details</h4>
                    <div>
                        <a href="index.php?controller=medical_record&action=my_records" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Records
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Record ID</h6>
                            <p class="fw-bold">#<?php echo $medical_record['id']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Record Date</h6>
                            <p class="fw-bold"><?php echo date('F d, Y', strtotime($medical_record['record_date'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Record Type</h6>
                            <span class="badge bg-<?php 
                                switch($medical_record['record_type']) {
                                    case 'general': echo 'primary'; break;
                                    case 'chronic': echo 'warning'; break;
                                    case 'emergency': echo 'danger'; break;
                                    case 'vaccination': echo 'success'; break;
                                    default: echo 'secondary';
                                }
                            ?> fs-6">
                                <?php echo ucfirst($medical_record['record_type']); ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Created</h6>
                            <p class="mb-0"><?php echo date('F d, Y H:i A', strtotime($medical_record['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted">Title</h6>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($medical_record['title']); ?></h5>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted">Description</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($medical_record['description'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Doctor</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-md text-primary"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0">Dr. <?php echo htmlspecialchars($medical_record['doctor_first_name'] . ' ' . $medical_record['doctor_last_name']); ?></p>
                                    <small class="text-muted"><?php echo htmlspecialchars($medical_record['specialization']); ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Patient</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0"><?php echo htmlspecialchars($medical_record['first_name'] . ' ' . $medical_record['last_name']); ?></p>
                                    <small class="text-muted">Health ID: <?php echo htmlspecialchars($medical_record['health_id']); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($medical_record['file_path'])): ?>
                        <div class="mb-4">
                            <h6 class="text-muted">Attached File</h6>
                            <div class="bg-light p-3 rounded d-flex align-items-center">
                                <i class="fas fa-paperclip text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold"><?php echo basename($medical_record['file_path']); ?></p>
                                    <small class="text-muted">Click to download</small>
                                </div>
                                <a href="<?php echo htmlspecialchars($medical_record['file_path']); ?>" class="btn btn-sm btn-outline-primary" download>
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="index.php?controller=medical_record&action=download&id=<?php echo $medical_record['id']; ?>" class="btn btn-success">
                                <i class="fas fa-download me-1"></i>Download Record
                            </a>
                        </div>
                        <div>
                            <small class="text-muted">
                                Last updated: <?php echo date('F d, Y H:i A', strtotime($medical_record['updated_at'] ?? $medical_record['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-start {
    border-left-width: 4px !important;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.card-footer {
    border-top: 1px solid #dee2e6;
}

.btn-group .btn {
    border-radius: 0.375rem;
}
</style>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
