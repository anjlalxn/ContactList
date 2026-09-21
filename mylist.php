<?php

// Connect to database
$conn = mysqli_connect('localhost', 'root', '', 'mycontacts');

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}


// ============================================
// CREATE / DELETE / EDIT
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ========================================
    // CREATE
    // ========================================

    if (isset($_POST['contactNum'])) {

        $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO contacts (Number, Name, Relationship, Carrier)
             VALUES (?, ?, ?, ?)'
        );

        mysqli_stmt_bind_param(
                $stmt,
                'ssss',
                $number,
                $name,
                $relationship,
                $carrier
        );

        $number       = $_POST['contactNum'];
        $name         = $_POST['contactName'];
        $relationship = $_POST['contactRelationship'];
        $carrier      = $_POST['contactCarrier'];

        if (mysqli_stmt_execute($stmt)) {

            header('Location: mylist.php?status=added');
            exit;

        } else {

            $message = 'Error: ' . mysqli_stmt_error($stmt);
            $bg = 'bg-danger';

        }

        mysqli_stmt_close($stmt);


        // ========================================
        // DELETE ALL
        // ========================================

    } elseif (isset($_POST['deleteAllContact'])) {

        $stmt = mysqli_prepare(
                $conn,
                'DELETE FROM contacts'
        );

        if (mysqli_stmt_execute($stmt)) {

            header('Location: mylist.php?status=deleted');
            exit;

        } else {

            $message = 'Error: ' . mysqli_stmt_error($stmt);
            $bg = 'bg-danger';

        }

        mysqli_stmt_close($stmt);


        // ========================================
        // DELETE SINGLE
        // ========================================

    } elseif (isset($_POST['deleteSingleContact'])) {

        $number = $_POST['number'];

        $stmt = mysqli_prepare(
                $conn,
                'DELETE FROM contacts WHERE Number = ?'
        );

        mysqli_stmt_bind_param(
                $stmt,
                's',
                $number
        );

        if (mysqli_stmt_execute($stmt)) {

            header('Location: mylist.php?status=deleted_individual');
            exit;

        } else {

            $message = 'Error: ' . mysqli_stmt_error($stmt);
            $bg = 'bg-danger';

        }

        mysqli_stmt_close($stmt);


        // ========================================
        // EDIT
        // ========================================

    } elseif (isset($_POST['editContact'])) {

        // OLD number identifies the existing record
        $oldNumber = $_POST['oldNumber'];

        // NEW number is the edited number
        $number       = $_POST['number'];
        $name         = $_POST['name'];
        $relationship = $_POST['relationship'];
        $carrier      = $_POST['carrier'];


        $stmt = mysqli_prepare(
                $conn,
                'UPDATE contacts
             SET Number = ?, Name = ?, Relationship = ?, Carrier = ?
             WHERE Number = ?'
        );


        mysqli_stmt_bind_param(
                $stmt,
                'sssss',
                $number,
                $name,
                $relationship,
                $carrier,
                $oldNumber
        );


        if (mysqli_stmt_execute($stmt)) {

            header('Location: mylist.php?status=edited');
            exit;

        } else {

            $message = 'Error: ' . mysqli_stmt_error($stmt);
            $bg = 'bg-danger';

        }

        mysqli_stmt_close($stmt);
    }
}


// ============================================
// COUNT CONTACTS
// ============================================

$sql = "SELECT COUNT(*) AS total FROM contacts";

$countResult = mysqli_query($conn, $sql);

if (!$countResult) {
    die('Count query failed: ' . mysqli_error($conn));
}

$countRow = mysqli_fetch_assoc($countResult);

$contactCount = $countRow['total'];


// ============================================
// STATUS MESSAGES
// ============================================

$message = $message ?? '';
$bg = $bg ?? '';


if (isset($_GET['status']) && $_GET['status'] === 'added') {

    $message = 'Added contact successfully';
    $bg = 'bg-success';

}


if (isset($_GET['status']) && $_GET['status'] === 'deleted') {

    $message = 'Deleted all contacts successfully';
    $bg = 'bg-success';

}


if (isset($_GET['status']) && $_GET['status'] === 'deleted_individual') {

    $message = 'Deleted contact successfully';
    $bg = 'bg-success';

}


if (isset($_GET['status']) && $_GET['status'] === 'edited') {

    $message = 'Edited contact successfully';
    $bg = 'bg-success';

}


// ============================================
// GET CONTACTS
// ============================================

$sql = 'SELECT Number, Name, Relationship, Carrier FROM contacts';

$result = mysqli_query($conn, $sql);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

?>

    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport"
              content="width=device-width, initial-scale=1">

        <title>My Contacts</title>


        <!-- GOOGLE FONT -->

        <link rel="preconnect"
              href="https://fonts.googleapis.com">

        <link rel="preconnect"
              href="https://fonts.gstatic.com"
              crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
              rel="stylesheet">


        <!-- BOOTSTRAP -->

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
              rel="stylesheet">


        <!-- CUSTOM CSS -->

        <link href="style.css"
              rel="stylesheet">

    </head>


    <body>


    <!-- ============================================ -->
    <!-- HEADER -->
    <!-- ============================================ -->

    <div class="container mt-4">

        <div class="border-bottom pb-3 mb-4">

            <h2 class="fw-bold mb-1">

                My Contacts

            </h2>

            <p class="text-muted mb-0">

                Manage your saved contacts

            </p>

        </div>


        <!-- TOTAL CONTACTS -->

        <div class="card bg-dark text-white">

            <div class="card-body">

                <h2 class="fw-bold">

                    <?= htmlspecialchars($contactCount) ?>

                </h2>

                <p class="text-white mb-0">

                    Total saved contacts

                </p>

            </div>

        </div>

    </div>



    <!-- ============================================ -->
    <!-- TOAST -->
    <!-- ============================================ -->

    <?php if (!empty($message)): ?>

        <div class="toast-container position-fixed top-0 end-0 p-3">

            <div id="contactToast"
                 class="toast align-items-center text-white <?= htmlspecialchars($bg) ?> border-0"
                 role="alert"
                 aria-live="assertive"
                 aria-atomic="true">

                <div class="d-flex">

                    <div class="toast-body">

                        <?= htmlspecialchars($message) ?>

                    </div>

                    <button type="button"
                            class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"
                            aria-label="Close">

                    </button>

                </div>

            </div>

        </div>

    <?php endif; ?>



    <div class="container py-5">


        <!-- ============================================ -->
        <!-- BUTTONS -->
        <!-- ============================================ -->


        <!-- ADD CONTACT -->

        <button type="button"
                class="btn btn-success mb-3"
                data-bs-toggle="modal"
                data-bs-target="#addContactModal">

            Add Contact

        </button>


        <!-- DELETE ALL -->

        <button type="button"
                class="btn btn-danger mb-3"
                data-bs-toggle="modal"
                data-bs-target="#deleteAllContactModal">

            Delete All

        </button>


        <!-- DOWNLOAD -->

        <button type="button"
                id="download-btn"
                class="btn btn-dark mb-3">

            Download List

        </button>



        <!-- ============================================ -->
        <!-- EDIT CONTACT MODAL -->
        <!-- ============================================ -->

        <div class="modal fade"
             id="editContactModal"
             tabindex="-1"
             aria-labelledby="editContactLabel"
             aria-hidden="true">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form method="post"
                          action="mylist.php">


                        <!-- MODAL HEADER -->

                        <div class="modal-header">

                            <h5 class="modal-title"
                                id="editContactLabel">

                                Edit Contact

                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close">

                            </button>

                        </div>


                        <!-- MODAL BODY -->

                        <div class="modal-body">


                            <!-- NUMBER -->

                            <div class="mb-3">

                                <label for="editNumber"
                                       class="form-label">

                                    Number

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="editNumber"
                                       name="number"
                                       maxlength="11"
                                       required>

                            </div>


                            <!-- OLD NUMBER -->

                            <input type="hidden"
                                   id="oldNumber"
                                   name="oldNumber">


                            <!-- NAME -->

                            <div class="mb-3">

                                <label for="editName"
                                       class="form-label">

                                    Name

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="editName"
                                       name="name"
                                       required>

                            </div>


                            <!-- RELATIONSHIP -->

                            <div class="mb-3">

                                <label for="editRelationship"
                                       class="form-label">

                                    Relationship

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="editRelationship"
                                       name="relationship"
                                       required>

                            </div>


                            <!-- CARRIER -->

                            <div class="mb-3">

                                <label for="editCarrier"
                                       class="form-label">

                                    Carrier

                                </label>

                                <select class="form-select"
                                        id="editCarrier"
                                        name="carrier"
                                        required>

                                    <option value="Globe">
                                        Globe
                                    </option>

                                    <option value="Smart">
                                        Smart
                                    </option>

                                    <option value="TNT">
                                        TNT
                                    </option>

                                    <option value="TM">
                                        TM
                                    </option>

                                    <option value="DITO">
                                        DITO
                                    </option>

                                    <option value="Sun">
                                        Sun
                                    </option>

                                </select>

                            </div>


                            <!-- IDENTIFY EDIT REQUEST -->

                            <input type="hidden"
                                   name="editContact"
                                   value="editContact">

                        </div>


                        <!-- MODAL FOOTER -->

                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button type="submit"
                                    class="btn btn-warning">

                                Save Changes

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>



        <!-- ============================================ -->
        <!-- DELETE ALL MODAL -->
        <!-- ============================================ -->

        <div class="modal fade"
             id="deleteAllContactModal"
             tabindex="-1"
             aria-labelledby="deleteAllContactLabel"
             aria-hidden="true">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form method="post"
                          action="mylist.php">


                        <!-- MODAL HEADER -->

                        <div class="modal-header">

                            <h5 class="modal-title"
                                id="deleteAllContactLabel">

                                Delete All Contacts

                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close">

                            </button>

                        </div>


                        <!-- MODAL BODY -->

                        <div class="modal-body">

                            <p>

                                Are you sure you want to delete
                                <strong>all contacts</strong>?

                            </p>

                            <p class="text-danger mb-0">

                                This action cannot be undone.

                            </p>


                            <input type="hidden"
                                   name="deleteAllContact"
                                   value="deleteAllContact">

                        </div>


                        <!-- MODAL FOOTER -->

                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button type="submit"
                                    class="btn btn-danger">

                                Delete All

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>



        <!-- ============================================ -->
        <!-- ADD CONTACT MODAL -->
        <!-- ============================================ -->

        <div class="modal fade"
             id="addContactModal"
             tabindex="-1"
             aria-labelledby="addContactLabel"
             aria-hidden="true">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form method="post"
                          action="mylist.php">


                        <!-- MODAL HEADER -->

                        <div class="modal-header">

                            <h5 class="modal-title"
                                id="addContactLabel">

                                Add Contact

                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close">

                            </button>

                        </div>


                        <!-- MODAL BODY -->

                        <div class="modal-body">


                            <!-- NUMBER -->

                            <div class="mb-3">

                                <label for="contactNum"
                                       class="form-label">

                                    Number

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="contactNum"
                                       name="contactNum"
                                       maxlength="11"
                                       placeholder="Enter number"
                                       required>

                            </div>


                            <!-- NAME -->

                            <div class="mb-3">

                                <label for="contactName"
                                       class="form-label">

                                    Name

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="contactName"
                                       name="contactName"
                                       placeholder="Enter name"
                                       required>

                            </div>


                            <!-- RELATIONSHIP -->

                            <div class="mb-3">

                                <label for="contactRelationship"
                                       class="form-label">

                                    Relationship

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="contactRelationship"
                                       name="contactRelationship"
                                       placeholder="e.g. Friend"
                                       required>

                            </div>


                            <!-- CARRIER -->

                            <div class="mb-3">

                                <label for="contactCarrier"
                                       class="form-label">

                                    Carrier

                                </label>

                                <select class="form-select"
                                        id="contactCarrier"
                                        name="contactCarrier"
                                        required>

                                    <option value=""
                                            selected
                                            disabled>

                                        Select carrier

                                    </option>

                                    <option value="Globe">
                                        Globe
                                    </option>

                                    <option value="Smart">
                                        Smart
                                    </option>

                                    <option value="TNT">
                                        TNT
                                    </option>

                                    <option value="TM">
                                        TM
                                    </option>

                                    <option value="DITO">
                                        DITO
                                    </option>

                                    <option value="Sun">
                                        Sun
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- MODAL FOOTER -->

                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button type="submit"
                                    class="btn btn-info">

                                Submit

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>



        <!-- ============================================ -->
        <!-- CONTACT TABLE -->
        <!-- ============================================ -->

        <div class="card mt-3"
             id="contact-card">


            <!-- CARD HEADER -->

            <div class="card-header bg-info text-white">

                <h4 class="mb-0">

                    My Contacts

                </h4>

            </div>


            <!-- CARD BODY -->

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-striped table-hover align-middle">


                        <!-- TABLE HEADER -->

                        <thead class="table-light">

                        <tr>

                            <th>
                                Number
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Relationship
                            </th>

                            <th>
                                Carrier
                            </th>

                            <th class="pdf-hide">
                                Edit
                            </th>

                            <th class="pdf-hide">
                                Delete
                            </th>

                        </tr>

                        </thead>


                        <!-- TABLE CONTENT -->

                        <tbody>

                        <?php if (mysqli_num_rows($result) > 0): ?>


                            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                <tr>


                                    <!-- NUMBER -->

                                    <td>

                                        <?= htmlspecialchars($row['Number']) ?>

                                    </td>


                                    <!-- NAME -->

                                    <td>

                                        <?= htmlspecialchars($row['Name']) ?>

                                    </td>


                                    <!-- RELATIONSHIP -->

                                    <td>

                                        <?= htmlspecialchars($row['Relationship']) ?>

                                    </td>


                                    <!-- CARRIER -->

                                    <td>

                                        <?= htmlspecialchars($row['Carrier']) ?>

                                    </td>


                                    <!-- EDIT -->

                                    <td class="pdf-hide">

                                        <button type="button"
                                                class="btn btn-warning btn-sm edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editContactModal"
                                                data-number="<?= htmlspecialchars($row['Number']) ?>"
                                                data-name="<?= htmlspecialchars($row['Name']) ?>"
                                                data-relationship="<?= htmlspecialchars($row['Relationship']) ?>"
                                                data-carrier="<?= htmlspecialchars($row['Carrier']) ?>">

                                            Edit

                                        </button>

                                    </td>


                                    <!-- DELETE -->

                                    <td class="pdf-hide">

                                        <form method="post"
                                              action="mylist.php">

                                            <input type="hidden"
                                                   name="deleteSingleContact"
                                                   value="deleteSingleContact">

                                            <input type="hidden"
                                                   name="number"
                                                   value="<?= htmlspecialchars($row['Number']) ?>">

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endwhile; ?>


                        <?php else: ?>

                            <tr>

                                <td colspan="6"
                                    class="text-center text-muted">

                                    0 results

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>



    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->

    <div class="container">

        <footer class="py-3 my-4">

            <p class="text-center text-body-secondary">

                made by crimsoncrows on github

            </p>

        </footer>

    </div>



    <!-- ============================================ -->
    <!-- BOOTSTRAP JS -->
    <!-- ============================================ -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



    <!-- ============================================ -->
    <!-- HTML2PDF -->
    <!-- ============================================ -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>



    <!-- ============================================ -->
    <!-- TOAST -->
    <!-- ============================================ -->

    <script>

        const toastEl = document.getElementById('contactToast');

        if (toastEl) {

            const toast = new bootstrap.Toast(toastEl);

            toast.show();

        }

    </script>



    <!-- ============================================ -->
    <!-- EDIT MODAL -->
    <!-- ============================================ -->

    <script>

        const editButtons = document.querySelectorAll('.edit-btn');

        editButtons.forEach(function (button) {

            button.addEventListener('click', function () {

                // Put current number into editable Number field
                document.getElementById('editNumber').value =
                    button.dataset.number;


                // Keep original number hidden
                document.getElementById('oldNumber').value =
                    button.dataset.number;


                // Put current name into modal
                document.getElementById('editName').value =
                    button.dataset.name;


                // Put current relationship into modal
                document.getElementById('editRelationship').value =
                    button.dataset.relationship;


                // Put current carrier into modal
                document.getElementById('editCarrier').value =
                    button.dataset.carrier;

            });

        });

    </script>



    <!-- ============================================ -->
    <!-- PDF DOWNLOAD -->
    <!-- ============================================ -->

    <script>

        document.getElementById('download-btn').addEventListener('click', function () {

            const element = document.getElementById('contact-card');

            const hiddenElements = element.querySelectorAll('.pdf-hide');


            // Hide Edit and Delete columns

            hiddenElements.forEach(function (item) {

                item.style.display = 'none';

            });


            const options = {

                margin: 10,

                filename: 'my_contacts.pdf',

                image: {
                    type: 'jpeg',
                    quality: 0.98
                },

                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff'
                },

                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }

            };


            html2pdf()

                .set(options)

                .from(element)

                .save()

                .then(function () {

                    // Show Edit and Delete columns again

                    hiddenElements.forEach(function (item) {

                        item.style.display = '';

                    });

                });

        });

    </script>


    </body>

    </html>


<?php

mysqli_close($conn);

?>