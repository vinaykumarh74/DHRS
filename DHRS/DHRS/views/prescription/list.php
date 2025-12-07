<?php
// Prescription List View

$page_title = 'My Prescriptions';

ob_start();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Prescriptions</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (!empty($prescriptions)): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prescriptions as $p): ?>
                                <tr>
                                    <td>#<?php echo (int)$p['id']; ?></td>
                                    <td><?php echo format_date($p['created_at']); ?></td>
                                    <td><?php echo isset($p['citizen_name']) ? htmlspecialchars($p['citizen_name']) : '-'; ?></td>
                                    <td><?php echo isset($p['doctor_name']) ? htmlspecialchars($p['doctor_name']) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($p['diagnosis'] ?? ''); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary me-2" href="index.php?controller=prescription&action=view&id=<?php echo (int)$p['id']; ?>">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a class="btn btn-sm btn-primary" href="index.php?controller=prescription&action=download&id=<?php echo (int)$p['id']; ?>">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No prescriptions found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('views/layout.php');
?>


