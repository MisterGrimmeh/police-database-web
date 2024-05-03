<?php

require_once('config.php');

$page_title = "Dashboard";
include('header.php');

?>
<section>
    <header>
        <h1>Dashboard</h1>
    </header>
    <section>
        <header>
            <h2>Reports</h2>
        </header>
        <ul>
            <li><a href="show-complaint.php">Complaints</a></li>
            <li><a href="show-medical.php">Medical</a></li>
            <li><a href="show-arrest.php">Arrests</a></li>
        </ul>
    </section>
</section>
<?php

include('footer.php');

?>