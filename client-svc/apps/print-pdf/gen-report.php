<?php
    session_start();

    date_default_timezone_set("Asia/Manila");
    header('Content-Type: text/html; charset=utf-8');

    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/connect_db.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/chk_login.php';
    require $_SERVER['DOCUMENT_ROOT'].'/client-svc/utils/security_check.php';
    require_once 'tcpdf.php';

    if (!func::checkLoginState($con)) {
        header("Location: /client-svc/apps/admin/login.php");
        die();
    }

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Philhealth');
    // $pdf->SetTitle('Report');
    // $pdf->SetSubject('Report');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    // set default header data   0,64,255
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'PhilHealth@LHIO', date("Y-M-d"), array(0, 122, 62), array(0, 122, 0));
    $pdf->setFooterData(array(0, 122, 62), array(0, 122, 0));

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(8);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);
    $pdf->setPageOrientation('L');

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 9, '', true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    // set text shadow effect
    $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
        if (security::checkMethod("GET")) {
            input::$redirect_location = "reports.php";
            $start_date = input::checkDate(date('Y-m-d', strtotime($_GET["start_date"])));
            $end_date = input::checkDate(date('Y-m-d', strtotime($_GET["end_date"])));
        }
    }
    else {
        $start_date = date('Y-m-d', strtotime(date("Y-m")."-1"));
        $end_date = date('Y-m-d', strtotime(date("Y-m")."-".cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"))));
    }
    
    $frontliner_qry = "";
    if ($_SESSION["user_role"] == "FRONTLINER") {
        $frontliner_qry = "AND FRONTLINE_ID={$_SESSION['username']}";
    }
    
    $stmt = $con -> query("
        SELECT 
        TBLNOF.TRANSACTION_DATE 'Date', 
        TBLNOF.CLIENTS 'Clients',  
        SUM(
            (CASE WHEN (TBLEM.NEWMEMBER) IS NULL THEN '0' ELSE TBLEM.NEWMEMBER END ) + 
            (CASE WHEN (TBLEE.NEWEMPLOYERS) IS NULL THEN '0' ELSE TBLEE.NEWEMPLOYERS END ) +
            (CASE WHEN (TBLUM.MEMBERUPDATES) IS NULL THEN '0' ELSE TBLUM.MEMBERUPDATES END ) +
            (CASE WHEN (TBLUE.EMPLOYERSUPDATES) IS NULL THEN '0' ELSE TBLUE.EMPLOYERSUPDATES END ) +
            (CASE WHEN (TBLMDR.MDR) IS NULL THEN '0' ELSE TBLMDR.MDR END ) + 
            (CASE WHEN (TBLID.IDCOUNT) IS NULL THEN '0' ELSE TBLID.IDCOUNT END ) +
            (CASE WHEN (TBLPC.CONTRIBUTION) IS NULL THEN '0' ELSE TBLPC.CONTRIBUTION END)
        ) 'Number of Total Transactions', 
        (CASE WHEN (TBLEM.NEWMEMBER) IS NULL THEN '0' ELSE TBLEM.NEWMEMBER END ) 'New Member', 
        (CASE WHEN (TBLEE.NEWEMPLOYERS) IS NULL THEN '0' ELSE TBLEE.NEWEMPLOYERS END ) 'New Employer', 
        (CASE WHEN (TBLUM.MEMBERUPDATES) IS NULL THEN '0' ELSE TBLUM.MEMBERUPDATES END ) 'Member', 
        (CASE WHEN (TBLUE.EMPLOYERSUPDATES) IS NULL THEN '0' ELSE TBLUE.EMPLOYERSUPDATES END ) 'Employer', 
        (CASE WHEN (TBLMDR.MDR) IS NULL THEN '0' ELSE TBLMDR.MDR END ) 'MDR', 
        (CASE WHEN (TBLID.IDCOUNT) IS NULL THEN '0' ELSE TBLID.IDCOUNT END ) 'ID', 
        (CASE WHEN (TBLPC.CONTRIBUTION) IS NULL THEN '0' ELSE TBLPC.CONTRIBUTION END) 'Premium Contrib'

        FROM (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(HDID) IS NULL THEN '0' ELSE COUNT(HDID) END) CLIENTS FROM `tbl_transaction` 
            WHERE STATUS='DONE' $frontliner_qry AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE ORDER BY TRANSACTION_DATE ASC
        ) TBLNOF

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) NEWMEMBER 
            FROM `tbl_transaction` WHERE PIN_PEN='NEW_MEMBER' $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE ORDER BY TRANSACTION_DATE ASC
        ) TBLEM ON TBLNOF.TRANSACTION_DATE=TBLEM.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) NEWEMPLOYERS 
            FROM `tbl_transaction` WHERE PIN_PEN='NEW_EMPLOYER' $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC

        ) TBLEE ON TBLNOF.TRANSACTION_DATE=TBLEE.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN SUM(MEMBER_UPDATE_COUNT) IS NULL THEN '0' ELSE SUM(MEMBER_UPDATE_COUNT) END) MEMBERUPDATES FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%MEMBER UPDATING%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLUM ON TBLNOF.TRANSACTION_DATE=TBLUM.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) EMPLOYERSUPDATES FROM `tbl_transaction` 
            WHERE CLIENT_TYPE='EMPLOYER' AND TRANSACTION_DESC LIKE ('%RECORD UPDATING%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLUE ON TBLNOF.TRANSACTION_DATE=TBLUE.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) MDR FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%MEMBER DATA RECORD%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLMDR ON TBLNOF.TRANSACTION_DATE=TBLMDR.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) IDCOUNT FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%PNC/ID CARD%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLID ON TBLNOF.TRANSACTION_DATE=TBLID.TRANSACTION_DATE 

        LEFT JOIN (
            SELECT TRANSACTION_DATE, (CASE WHEN COUNT(ID) IS NULL THEN '0' ELSE COUNT(ID) END) CONTRIBUTION FROM `tbl_transaction` 
            WHERE TRANSACTION_DESC LIKE ('%PREMIUM CONTRIBUTION%') $frontliner_qry AND STATUS='DONE' AND TRANSACTION_DATE 
            BETWEEN '$start_date' AND '$end_date' 
            GROUP BY TRANSACTION_DATE 
            ORDER BY TRANSACTION_DATE ASC
        ) TBLPC ON TBLNOF.TRANSACTION_DATE=TBLPC.TRANSACTION_DATE

        GROUP BY TBLNOF.TRANSACTION_DATE
    ");
    $stmt -> execute();

    $res = "";
    
    $num_clients = 0;
    $num_transactions = 0;
    $num_new_member = 0;
    $num_new_employer = 0;
    $num_member = 0;
    $num_employer = 0;
    $num_mdr = 0;
    $num_pnc = 0;
    $num_contrib = 0;

    while ($data = $stmt -> fetch()) {
        $res .= "
        <tr>
            <td>".date("M d", strtotime($data[0]))."</td>
            <td>{$data[1]}</td>
            <td>{$data[2]}</td>
            <td>{$data[3]}</td>
            <td>{$data[4]}</td>
            <td>{$data[5]}</td>
            <td>{$data[6]}</td>
            <td>{$data[7]}</td>
            <td>{$data[8]}</td>
            <td>{$data[9]}</td>
            <td></td>
        </tr>";
        $num_clients += $data[1];
        $num_transactions += $data[2];
        $num_new_member += $data[3];
        $num_new_employer += $data[4];
        $num_member += $data[5];
        $num_employer += $data[6];
        $num_mdr += $data[7];
        $num_pnc += $data[8];
        $num_contrib += $data[9];
    }

    $res .= "
    <tr>
        <td>TOTAL</td>
        <td>$num_clients</td>
        <td>$num_transactions</td>
        <td>$num_new_member</td>
        <td>$num_new_employer</td>
        <td>$num_member</td>
        <td>$num_employer</td>
        <td>$num_mdr</td>
        <td>$num_pnc</td>
        <td>$num_contrib</td>
        <td></td>
    </tr>";

    $html = '
        <table border="1" style="text-align: center;">
            <tr>
                <th colspan="11"><b>BAGUIO PHILHEALTH EXPRESS REPORT ('.strtoupper(date("F d, Y", strtotime($start_date))).' - '.strtoupper(date("F d, Y", strtotime($end_date))).')</b></th>
            </tr>
            <tr>
                <td rowspan="3">Date</td>
                <td rowspan="3">Num of Clients</td>
                <td rowspan="3">Total Transactions Done</td>
                <td colspan="7">Membership Transactions</td>
                <td rowspan="3">Remarks</td>
            </tr>
            <tr>
                <td colspan="2">Enrollment</td>
                <td colspan="2">Updating of Data</td>
                <td colspan="3">Printing</td>
            </tr>
            <tr>
                <td>Member</td>
                <td>Employer</td>
                <td>Member</td>
                <td>Employer</td>
                <td>MDR</td>
                <td>PNC/ID</td>
                <td>Contrib. Hist.</td>
            </tr>
            '.$res.'
        </table>';
        if ($_SESSION['user_role'] == "FRONTLINER") {
            $html .= '
            <br><br>
            <table style="text-align: center;">
                <tr>
                    <td style="text-align: left;">Prepared by:</td>
                    <td style="text-align: left;">Noted by:</td>
                </tr>
                <tr>
                <td></td>
                <td></td>
                </tr>
                <tr style="padding-left: 200px;text-align: left;">
                    <td><b>'.$_SESSION["username"].'</b> <i>(Clerk)</i></td>
                    <td><b>Catalina C. Adawey</b> <i>(CSO/LHIO Head)</i></td>
                </tr>
            </table>';
        }
    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------
    $date = date("y-m-d his");
    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output("$date.pdf", 'I');

    // NUMBER OF CLIENTS = NUMBER OF THE SAME HDID (FILTERED BY DATE)
    // TOTAL DONE TRANSACTIONS = NUMBER TRANSCATION_DESC SEPARATED BY SEMICOLONS (FILTERED BY DONE AND CURRENT DATE)
    
    // (ENROLLMENT)
    // > MEMBER = NUMBER OF NEW_MEMBER (FILTERED BY DATE)
    // > EMPLOYER = NUMBER OF NEW_EMPLOYER (FILTERED BY DATE)
    
    // (UPDATING OF DATA)
    // > MEMBER = COUNT OF MEMBER UPDATING OF OLD MEMBER (FILTERED BY DATE)
    // > EMPLOYER = GET EMP_MEMBERS UPDATING (FILTERED BY DATE)
    
    // PRINTING
    // > MDR = TOTAL NUMBER OF MEMBER DATA RECORD (FILTERED BY DATE)
    // > PNC/ID = TOTAL NUMBER OF PNC/ID TRANSACTIONS (FILTERED BY DATE)
    // > CONTRIBUTION HISTORY = NUMBER OF PREMIUM CONTRIBUTION (FILTERED BY DATE)