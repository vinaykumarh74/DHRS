<?php
// Prescription Details View

$page_title = 'Prescription Details';

ob_start();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Prescription #<?php echo (int)$prescription['id']; ?></h1>
        <div>
            <a class="btn btn-outline-secondary me-2" href="index.php?controller=prescription&action=list"><i class="fas fa-list me-1"></i>Back to List</a>
            <a class="btn btn-primary" href="index.php?controller=prescription&action=download&id=<?php echo (int)$prescription['id']; ?>"><i class="fas fa-download me-1"></i>Download</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Patient Information</h5>
                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($prescription['citizen_name']); ?></p>
                    <p class="mb-1"><strong>Health ID:</strong> <?php echo htmlspecialchars($prescription['health_id']); ?></p>
                    <p class="mb-1"><strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($prescription['gender'])); ?></p>
                    <p class="mb-0"><strong>DOB:</strong> <?php echo format_date($prescription['date_of_birth']); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Doctor Information</h5>
                    <p class="mb-1"><strong>Name:</strong> Dr. <?php echo htmlspecialchars($prescription['doctor_name']); ?></p>
                    <p class="mb-1"><strong>Specialization:</strong> <?php echo htmlspecialchars($prescription['specialization']); ?></p>
                    <p class="mb-0"><strong>License #:</strong> <?php echo htmlspecialchars($prescription['license_number']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Prescription</h5>
            <p class="mb-1"><strong>Date:</strong> <?php echo format_date($prescription['created_at']); ?></p>
            <p class="mb-1"><strong>Diagnosis:</strong> <?php echo htmlspecialchars($prescription['diagnosis']); ?></p>
            <p class="mb-1"><strong>Instructions:</strong> <?php echo htmlspecialchars($prescription['instructions']); ?></p>
            <?php if (!empty($prescription['follow_up_date'])): ?>
                <p class="mb-0"><strong>Follow-up:</strong> <?php echo format_date($prescription['follow_up_date']); ?></p>
            <?php endif; ?>
            <?php if (!empty($prescription['notes'])): ?>
                <p class="mt-2 mb-0"><strong>Notes:</strong> <?php echo htmlspecialchars($prescription['notes']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Medications</h5>
            <?php if (!empty($medications)): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th>Special Instructions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medications as $m): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($m['medication_name']); ?></td>
                                    <td><?php echo htmlspecialchars($m['dosage']); ?></td>
                                    <td><?php echo htmlspecialchars($m['frequency']); ?></td>
                                    <td><?php echo htmlspecialchars($m['duration']); ?></td>
                                    <td><?php echo htmlspecialchars($m['special_instructions']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No medications recorded.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('views/layout.php');
?>


