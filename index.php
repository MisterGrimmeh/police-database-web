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
            <ul>
                <li><a href="show-open-complaint.php">open complaints only</a></li>
                <li><a href="show-complaint-medical.php">complaints with medical</a></li>
            </ul>
            <li><a href="show-medical.php">Medical</a></li>
            <li><a href="show-arrest.php">Arrests</a></li>
        </ul>
    </section>
    <section>
        <header>
            <h2>Management</h2>
        </header>
        <ul>
            <li><a href="show-entity.php">Identities</a></li>
            <li><a href="show-location.php">Locations</a></li>
            <li><a href="show-address.php">Addresses</a></li>
            <li><a href="event-crud.php">Events</a></li>
        </ul>
    </section>
</section>
<?php

include('footer.php');

?>