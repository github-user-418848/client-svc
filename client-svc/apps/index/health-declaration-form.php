<?php
    session_start();
    
    if (isset($_SESSION["queue_generated"])) {
        header("Location: /client-svc/apps/transactions/gen-queue.php");
        die();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/query.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/block-content.php';

    if (isset($_COOKIE["hdf_dat"]) && isset($_COOKIE["p_type"])) {
        $data = query::pdoSelectQuery("SELECT * FROM tbl_health_declaration WHERE TOKEN=:token", array(":token" => $_COOKIE['hdf_dat']), $con);

        if ($data) {
            $_SESSION["hdid"] = $data["ID"];
            $_SESSION["requestor_name"] = $data["LASTNAME"].', '.$data["FIRSTNAME"].' '.$data["MIDDLENAME"];
            $_SESSION["priority"] = $_COOKIE["p_type"];

            header("Location: client-type.php");
        }
    }

?>
<div class="alert alert-info text-center mt-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-question-circle-fill me-2" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
    </svg>
    This site uses cookies to provide you with a great user experience. By continuing to use this site, you accept our use of cookies
</div>
<h2 class="text-center">Health Declaration Form</h2>
<form method="post" action="health-declaration-form-handler.php" class="bg-white border p-4" id="form">
    <div class="row">
        <div class="col-lg-6">
            <h3>Info</h3>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="last_name" id="last_name" placeholder="">
                <label for="last_name">Last Name</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="first_name" id="first_name" placeholder="">
                <label for="first_name">First Name</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="mid_name" id="mid_name" placeholder="">
                <label for="mid_name">Middle Name</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="addr" id="addr" placeholder="">
                <label for="addr">Address</label>
                <div class="invalid-feedback">
                    This field is required.
                </div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="contact" id="contact" placeholder="">
                <label for="contact">Contact Number</label>
                <div class="invalid-feedback" id="invalid-feedback-num"></div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="text" name="temperature" id="temperature" placeholder="">
                <label for="temperature fw-bold">Temperature</label>
                <div class="invalid-feedback" id="invalid-feedback-decimal"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <h3>Symptoms</h3>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Fever
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="symptom_fever" value="Yes" id="btnradio1" autocomplete="off" required>
                        <label class="btn btn-outline-dark" for="btnradio1">Yes</label>
                        <input type="radio" class="btn-check" name="symptom_fever" value="No" id="btnradio2" autocomplete="off">
                        <label class="btn btn-outline-dark" for="btnradio2">No</label>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cough
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="symptom_cough" value="Yes" id="btnradio3" autocomplete="off" required>
                        <label class="btn btn-outline-dark" for="btnradio3">Yes</label>
                        <input type="radio" class="btn-check" name="symptom_cough" value="No" id="btnradio4" autocomplete="off">
                        <label class="btn btn-outline-dark" for="btnradio4">No</label>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Colds
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="symptom_colds" value="Yes" id="btnradio5" autocomplete="off" required>
                        <label class="btn btn-outline-dark" for="btnradio5">Yes</label>
                        <input type="radio" class="btn-check" name="symptom_colds" value="No" id="btnradio6" autocomplete="off">
                        <label class="btn btn-outline-dark" for="btnradio6">No</label>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Sore Throat
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="symptom_sore" value="Yes" id="btnradio7" autocomplete="off" required>
                        <label class="btn btn-outline-dark" for="btnradio7">Yes</label>
                        <input type="radio" class="btn-check" name="symptom_sore" value="No" id="btnradio8" autocomplete="off">
                        <label class="btn btn-outline-dark" for="btnradio8">No</label>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Shortness of Breath
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="symptom_short" value="Yes" id="btnradio9" autocomplete="off" required>
                        <label class="btn btn-outline-dark" for="btnradio9">Yes</label>
                        <input type="radio" class="btn-check" name="symptom_short" value="No" id="btnradio10" autocomplete="off">
                        <label class="btn btn-outline-dark" for="btnradio10">No</label>
                    </div>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Diarrhea
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="symptom_diarrhea" value="Yes" id="btnradio11" autocomplete="off" required>
                        <label class="btn btn-outline-dark" for="btnradio11">Yes</label>
                        <input type="radio" class="btn-check" name="symptom_diarrhea" value="No" id="btnradio12" autocomplete="off">
                        <label class="btn btn-outline-dark" for="btnradio12">No</label>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <h3 class="mt-2">Others</h3>
    <ul class="list-group mt-2 mb-3">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Are you a frontliner?
            <div class="btn-group" role="group" aria-label="Frontliner">
                <input type="radio" class="btn-check" name="frontliner" value="Yes" id="btnradio13" autocomplete="off" required>
                <label class="btn btn-outline-dark" for="btnradio13">Yes</label>
                <input type="radio" class="btn-check" name="frontliner" value="No" id="btnradio14" autocomplete="off">
                <label class="btn btn-outline-dark" for="btnradio14">No</label>
            </div>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Are you a contact of a suspected CoViD?
            <div class="btn-group" role="group" aria-label="Contact">
                <input type="radio" class="btn-check" name="contact2" value="Yes" id="btnradio15" autocomplete="off" required>
                <label class="btn btn-outline-dark" for="btnradio15">Yes</label>
                <input type="radio" class="btn-check" name="contact2" value="No" id="btnradio16" autocomplete="off">
                <label class="btn btn-outline-dark" for="btnradio16">No</label>
            </div>
        </li>
    </ul>
    <h3>Category</h3>
    <div class="form-floating mb-3">
        <select class="form-select" name="priority_type" id="priority_type" aria-label="Priority Type">
            <option value="Regular">Regular</option>
            <option value="Senior">Senior</option>
            <option value="Pregnant/Nursing">Pregnant/Nursing</option>
            <option value="P.W.D">P.W.D</option>
        </select>
        <label for="priority_type">Priority Type</label>
    </div>
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo security::genCSRF();?>">
    <input type="submit" value="Submit" class="btn btn-dark btn-lg w-100" name="submit">
</form>
<script type="text/javascript" src="<?php echo STATIC_URL; ?>js/form-validation.js"></script>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/client-svc/snippets/endblock.php';