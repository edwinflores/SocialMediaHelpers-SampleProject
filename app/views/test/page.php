<?php $title = 'Signup Test' ?>

<?php if (!isset($_SESSION['open_session_id'])): ?>
    <h3>Try out the signup links below:</h3>
    <a href="<?php eh(url("test/fb_login")) ?>">Facebook Login</a>
    <br>
    <a href="<?php eh(url("test/twitter_login")) ?>">Twitter Login</a>
    <br>
    <a href="<?php eh(url("test/google_login")) ?>">Google Login</a>
<?php else: ?>
    <strong><h4>User Data:</h4></strong>
    <?php var_dump($_SESSION['usr_dt']) ?>
    <br>
    <a class="btn btn-danger" href="<?php eh(url('test/logout'))?>">Log Out</a>
<?php endif ?>