<?php 

include '../views/header.php'; ?>
<main>
    <h1>Database Error</h1>
    <p>There was an error connecting to the database.</p>
    <p>The database must be set up according to the database_script.</p>
    <p>Error message: <?php echo $error_message; ?></p>
</main>
<?php include '../views/footer.php'; ?>