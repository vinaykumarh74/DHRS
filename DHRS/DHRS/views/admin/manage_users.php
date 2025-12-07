<?php
$page_title = 'Manage Users';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4>Manage Users</h4>
                    <div>
                        <a href="index.php?controller=admin&action=export_users" class="btn btn-success me-2">
                            <i class="fas fa-file-excel me-1"></i> Export to Excel
                        </a>
                        <a href="index.php?controller=admin&action=dashboard" class="btn btn-light">Back to Dashboard</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Total Users: <span class="badge bg-primary"><?php echo count($users); ?></span></h6>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Last updated: <?php echo date('M d, Y H:i:s'); ?></small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo $user['username']; ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td><?php echo $user['phone']; ?></td>
                                            <td>
                                                <span class="badge <?php echo ($user['role'] == 'admin') ? 'bg-danger' : (($user['role'] == 'doctor') ? 'bg-success' : 'bg-info'); ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo ($user['status'] == 'active') ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="index.php?controller=admin&action=edit_user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <?php if ($user['status'] == 'active'): ?>
                                                        <a href="index.php?controller=admin&action=deactivate_user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure you want to deactivate this user?');">Deactivate</a>
                                                    <?php else: ?>
                                                        <a href="index.php?controller=admin&action=activate_user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to activate this user?');">Activate</a>
                                                    <?php endif; ?>
                                                    <?php if ($user['role'] != 'admin'): ?>
                                                        <a href="index.php?controller=admin&action=delete_user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>