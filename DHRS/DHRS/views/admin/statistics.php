<?php
$page_title = 'System Statistics';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4>System Statistics</h4>
                    <a href="index.php?controller=admin&action=dashboard" class="btn btn-light">Back to Dashboard</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h1 class="display-4"><?php echo $user_stats['total_users']; ?></h1>
                                    <h5 class="card-title">Total Users</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h1 class="display-4"><?php echo $user_stats['total_citizens']; ?></h1>
                                    <h5 class="card-title">Total Citizens</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h1 class="display-4"><?php echo $user_stats['total_doctors']; ?></h1>
                                    <h5 class="card-title">Total Doctors</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5>System Health</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Metric</th>
                                                    <th>Value</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Database Connection</td>
                                                    <td>Connected</td>
                                                    <td><span class="badge bg-success">Good</span></td>
                                                </tr>
                                                <tr>
                                                    <td>PHP Version</td>
                                                    <td><?php echo phpversion(); ?></td>
                                                    <td><span class="badge bg-success">Good</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Server Time</td>
                                                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                                    <td><span class="badge bg-success">Good</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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