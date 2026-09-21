<?php
// Connect (mysqli procedural: host, username, password, database)
$conn = mysqli_connect('localhost', 'root', '', 'mycontacts');

// Always check the connection before running queries
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}


// CREATE / DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CREATE
    if (isset($_POST['contactNum'])) {

        $stmt = $conn->prepare(
                'INSERT INTO contacts (Number, Name, Relationship, Carrier) VALUES (?, ?, ?, ?)'
        );

        $stmt->bind_param(
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

        if ($stmt->execute()) {
            header('Location: mylist.php?status=added');
            exit;
        } else {
            $message = 'Error: ' . $stmt->error;
            $bg = 'bg-danger';
        }

        $stmt->close();


        // DELETE ALL
    } elseif (isset($_POST['deleteAllContact'])) {

        $stmt = $conn->prepare('DELETE FROM contacts');

        if ($stmt->execute()) {
            header('Location: mylist.php?status=deleted');
            exit;
        } else {
            $message = 'Error: ' . $stmt->error;
            $bg = 'bg-danger';
        }

        $stmt->close();


        // DELETE SINGLE
    } elseif (isset($_POST['deleteSingleContact'])) {

        $number = $_POST['number'];

        $stmt = $conn->prepare(
                'DELETE FROM contacts WHERE Number = ?'
        );

        $stmt->bind_param('s', $number);

        if ($stmt->execute()) {
            header('Location: mylist.php?status=deleted_individual');
            exit;
        } else {
            $message = 'Error: ' . $stmt->error;
            $bg = 'bg-danger';
        }

        $stmt->close();
    }
}


// COUNT TOTAL CONTACTS
$sql = "SELECT COUNT(*) AS total FROM contacts";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die('Count query failed: ' . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$contactCount = $row['total'];


// SHOW SUCCESS / ERROR MESSAGE
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


// GET CONTACTS
$sql = 'SELECT Number, Name, Relationship, Carrier FROM contacts';
$result = mysqli_query($conn, $sql);

// Never assume the query worked
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Contacts</title>

    <link href="style.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="border-bottom pb-3 mb-4">
        <h2 class="fw-bold mb-1">My Contacts</h2>
        <p class="text-muted mb-0">Manage your saved contacts</p>
    </div>

    <div class="card bg-success text-white">
        <div class="card-body">
            <h2 class="fw-bold"><?= $contactCount ?></h2>
            <p class="text-white mb-0">Total saved contacts</p>
        </div>
    </div>

</div>


<?php if (!empty($message)): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="contactToast"
             class="toast align-items-center text-white <?= $bg ?> border-0"
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

    <!-- ADD CONTACT BUTTON -->
    <button type="button"
            class="btn btn-info mb-3"
            data-bs-toggle="modal"
            data-bs-target="#addContactModal">
        Add Contact
    </button>


    <!-- DELETE ALL BUTTON -->
    <button type="button"
            class="btn btn-danger mb-3"
            data-bs-toggle="modal"
            data-bs-target="#deleteAllContactModal">
        Delete All
    </button>

    <button type="button"
            id="download-btn"
            class="btn btn-dark mb-3">
        Download List
    </button>



    <!-- DELETE ALL MODAL -->
    <div class="modal fade"
         id="deleteAllContactModal"
         tabindex="-1"
         aria-labelledby="deleteAllContactLabel"
         aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <form method="post" action="mylist.php">

                    <div class="modal-header">

                        <h5 class="modal-title" id="deleteAllContactLabel">
                            Delete All Contacts
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                        </button>

                    </div>


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


    <!-- ADD CONTACT MODAL -->
    <div class="modal fade"
         id="addContactModal"
         tabindex="-1"
         aria-labelledby="addContactLabel"
         aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <form method="post" action="mylist.php">

                    <div class="modal-header">

                        <h5 class="modal-title" id="addContactLabel">
                            Add Contact
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="mb-3">

                            <label for="contactNum" class="form-label">
                                Number
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="contactNum"
                                   name="contactNum"
                                   placeholder="Enter number (e.g. 09123456789)"
                                   maxlength="11"
                                   required>

                        </div>


                        <div class="mb-3">

                            <label for="contactName" class="form-label">
                                Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="contactName"
                                   name="contactName"
                                   placeholder="Enter full name"
                                   required>

                        </div>


                        <div class="mb-3">

                            <label for="contactRelationship" class="form-label">
                                Relationship
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="contactRelationship"
                                   name="contactRelationship"
                                   placeholder="e.g. Friend, Family, Classmate"
                                   required>

                        </div>


                        <div class="mb-3">

                            <label for="contactCarrier" class="form-label">
                                Carrier
                            </label>

                            <select class="form-select"
                                    id="contactCarrier"
                                    name="contactCarrier"
                                    required>

                                <option value="" selected disabled>
                                    Select carrier
                                </option>

                                <option value="Globe">Globe</option>
                                <option value="Smart">Smart</option>
                                <option value="TNT">TNT</option>
                                <option value="TM">TM</option>
                                <option value="DITO">DITO</option>
                                <option value="Sun">Sun</option>

                            </select>

                        </div>

                    </div>


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


    <!-- CONTACT TABLE -->
    <div class="card mt-3">

        <div class="card-header bg-info text-white">

            <h4 class="mb-0">
                My Contacts
            </h4>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-light">

                    <tr>
                        <th>Number</th>
                        <th>Name</th>
                        <th>Relationship</th>
                        <th>Carrier</th>
                        <th colspan="2" class="pdf-hide">Actions</th>
                    </tr>

                    </thead>


                    <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($row['Number']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['Name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['Relationship']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['Carrier']) ?>
                                </td>


                                <!-- EDIT -->
                                <td class="pdf-hide">

                                    <form method="post" action="mylist.php">

                                        <input type="hidden"
                                               name="action"
                                               value="edit">

                                        <input type="hidden"
                                               name="number"
                                               value="<?= htmlspecialchars($row['Number']) ?>">

                                        <button type="submit"
                                                class="btn btn-warning btn-sm">
                                            Edit
                                        </button>

                                    </form>

                                </td>


                                <!-- DELETE SINGLE -->
                                <td class="pdf-hide">

                                    <form method="post" action="mylist.php">

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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var toastEl = document.getElementById('contactToast');

    if (toastEl) {
        new bootstrap.Toast(toastEl).show();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    document.getElementById('download-btn').addEventListener('click', function () {

        const element = document.querySelector('.card.mt-3');

        const options = {
            margin: 10,
            filename: 'my_contacts.pdf',

            image: {
                type: 'png'
            },

            html2canvas: {
                scale: 3,
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
            .save();
    });
</script>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
