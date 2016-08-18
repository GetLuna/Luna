<?php

include( '../include/common.glt.php' );

include( 'include/header.php' );

?>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Welcome to Luna Preview</h1>
        <p class="lead">You're using the Backstage 7 Preview, for more settings, go back to the old Backstage.</p>
    </div>
</div>
<div class="container content">
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    Reports
                </div>
                <div class="card-block card-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Reported by</th>
                                <th>Date</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3">No unmanaged reports</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    Admin notes<span class="pull-xs-right"><a href="#" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> Save</a></span>
                </div>
                <div class="card-block">
                    <textarea class="form-control" rows="7" placeholder="Start typing..."><?php echo $config['o_admin_note'] ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card card-inverse card-danger">
                <div class="card-block">
                    <a href="../backstage" class="btn btn-secondary btn-block"><i class="fa fa-fw fa-angle-left"></i> Back to Backstage 6</a>
                </div>
            </div>
            <div class="card card-inverse card-success">
                <div class="card-header">
                    Luna is up-to-date
                </div>
                <div class="card-block">
                    <a href="#" class="btn btn-secondary btn-block"><i class="fa fa-fw fa-refresh"></i> Check for updates</a>
                </div>
            </div>
            <div class="card card-inverse card-primary card-statistics text-xs-center">
                <div class="card-block">
                    <div class="row">
                        <div class="col-xs-4">
                            <h4>548<small>Users</small></h4>
                        </div>
                        <div class="col-xs-4">
                            <h4>318<small>Threads</small></h4>
                        </div>
                        <div class="col-xs-4">
                            <h4>1 689<small>Comments</small></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-inverse card-warning">
                <div class="card-header">
                    Welcome to Luna Preview
                </div>
                <div class="card-block">
                    <p>Thank you for helping us build the next version of Luna. Here are some useful links.</p>
                    <div class="row row-padding">
                        <div class="col-xs-6">
                            <a href="https://github.com/GetLuna/Luna/issues/1111" class="btn btn-secondary btn-block"><i class="fa fa-fw fa-sticky-note-o"></i> Release notes</a>
                        </div>
                        <div class="col-xs-6">
                            <a href="https://github.com/GetLuna/Luna/issues/new" class="btn btn-secondary btn-block"><i class="fa fa-fw fa-github"></i> Report a bug</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include( 'include/footer.php' );