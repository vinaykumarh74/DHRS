<?php
/**
 * Doctor's Patients View
 */

// Set page title
$page_title = 'My Patients';

// Start output buffering
ob_start();
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title"><i class="fas fa-users text-primary me-2"></i> My Patients</h2>
            <!-- Flash messages are now handled by layout.php -->
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Search Form -->
            <form action="index.php" method="get" class="mb-4">
                <input type="hidden" name="controller" value="doctor">
                <input type="hidden" name="action" value="my_patients">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or health ID..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
            
            <!-- Patients List -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0">Total Patients: <strong><?php echo $total_patients; ?></strong></p>
            </div>
            
            <?php if (empty($patients)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No patients found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Health ID</th>
                                <th>Age/Gender</th>
                                <th>Contact</th>
                                <th>Last Visit</th>
                                <th>Total Visits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="patient-avatar me-2">
                                                <?php if (!empty($patient['profile_image'])): ?>
                                                    <img src="uploads/profile/<?php echo htmlspecialchars($patient['profile_image']); ?>" alt="Profile" class="rounded-circle">
                                                <?php else: ?>
                                                    <div class="avatar-placeholder rounded-circle bg-primary text-white">
                                                        <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($patient['health_id']); ?></td>
                                    <td>
                                        <?php echo calculate_age($patient['date_of_birth']); ?> yrs / 
                                        <?php echo ucfirst(htmlspecialchars($patient['gender'])); ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone-alt text-muted me-1"></i> <?php echo htmlspecialchars($patient['phone']); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($patient['last_visit'])): ?>
                                            <?php echo date('d M Y', strtotime($patient['last_visit'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">No visits</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $patient['appointment_count']; ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?controller=doctor&action=patient_details&id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Patient Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controller=prescription&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-outline-success" title="Create Prescription">
                                                <i class="fas fa-prescription"></i>
                                            </a>
                                            <a href="index.php?controller=medical_record&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-outline-info" title="Create Medical Record">
                                                <i class="fas fa-notes-medical"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?controller=doctor&action=my_patients&page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $start_page + 4);
                            if ($end_page - $start_page < 4 && $start_page > 1) {
                                $start_page = max(1, $end_page - 4);
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?controller=doctor&action=my_patients&page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?controller=doctor&action=my_patients&page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .patient-avatar img, .avatar-placeholder {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
</style>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
