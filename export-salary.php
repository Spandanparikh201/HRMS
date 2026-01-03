<?php
require_once 'autoload.php'; // Assuming this loads Dompdf
// Fallback if root autoload doesn't load Dompdf but the folder exists
if (!class_exists('Dompdf\Dompdf') && file_exists('dompdf/autoload.inc.php')) {
    require_once 'dompdf/autoload.inc.php';
}

use Dompdf\Dompdf;

$type = isset($_GET['type']) ? $_GET['type'] : 'csv';

if ($type == 'csv') {
    $filename = "salary_data_" . date('Ymd') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, array('Employee Name', 'Employee ID', 'Basic Salary', 'Allowances', 'Deductions', 'Net Salary', 'Pay Period'));
    
    // Static Data mirroring salary.php
    $data = [
        ['John Doe', 'FT-0001', '$6500', '$165', '$300', '$59698', 'Feb 2019'],
        ['Richard Miles', 'FT-0002', '$8000', '$200', '$1000', '$72000', 'Feb 2019'],
        ['John Smith', 'FT-0003', '$5500', '$150', '$200', '$48200', 'Feb 2019'],
        ['Mike Litorus', 'FT-0004', '$7000', '$180', '$400', '$59698', 'Feb 2019'],
        ['Wilmer Deluna', 'FT-0005', '$5000', '$120', '$300', '$43000', 'Feb 2019'],
        ['Jeffrey Warden', 'FT-0006', '$5200', '$140', '$250', '$45000', 'Feb 2019'],
        ['Bernardo Galaviz', 'FT-0007', '$4500', '$110', '$200', '$38400', 'Feb 2019'],
        ['Lesley Grauer', 'FT-0008', '$8500', '$220', '$600', '$75500', 'Feb 2019'],
        ['Jeffery Lalor', 'FT-0009', '$8200', '$210', '$550', '$73550', 'Feb 2019'],
        ['Loren Gatlin', 'FT-0010', '$6000', '$160', '$350', '$55000', 'Feb 2019'],
        ['Tarah Shropshire', 'FT-0011', '$9500', '$250', '$800', '$92400', 'Feb 2019'],
    ];
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();

} elseif ($type == 'pdf') {
    $dompdf = new Dompdf();
    
    // HTML Content matching salary-view.php
    // We add inline styles for PDF compatibility
    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Payslip</title>
        <style>
            body { font-family: "Helvetica", sans-serif; color: #333; }
            .header { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
            .logo { max-height: 50px; }
            .company-info { float: left; }
            .invoice-info { float: right; text-align: right; }
            .title { text-transform: uppercase; font-weight: bold; font-size: 24px; margin-bottom: 5px; }
            .emp-info { margin-bottom: 30px; }
            .emp-info ul { list-style: none; padding: 0; }
            .emp-info li { margin-bottom: 5px; }
            .earnings, .deductions { width: 48%; float: left; }
            .deductions { float: right; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
            .text-right { text-align: right; }
            .net-salary { clear: both; margin-top: 20px; font-weight: bold; border: 2px solid #333; padding: 10px; text-align: center; }
            .clear { clear: both; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="company-info">
                <!-- Using absolute path or base64 usually better for PDF -->
                <h3>Dayflow Technologies</h3>
                <p>3864 Quiet Valley Lane,<br>Sherman Oaks, CA, 91403</p>
            </div>
            <div class="invoice-info">
                <div class="title">Payslip #49029</div>
                <p>Salary Month: March, 2019</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="emp-info">
            <ul>
                <li><strong>John Doe</strong></li>
                <li>Web Designer</li>
                <li>Employee ID: FT-0009</li>
                <li>Joining Date: 1 Jan 2013</li>
            </ul>
        </div>

        <div class="row">
            <div class="earnings">
                <h4>Earnings</h4>
                <table>
                    <tr><td>Basic Salary</td><td class="text-right">$6500</td></tr>
                    <tr><td>House Rent Allowance (H.R.A.)</td><td class="text-right">$55</td></tr>
                    <tr><td>Conveyance</td><td class="text-right">$55</td></tr>
                    <tr><td>Other Allowance</td><td class="text-right">$55</td></tr>
                    <tr><td><strong>Total Earnings</strong></td><td class="text-right"><strong>$6665</strong></td></tr>
                </table>
            </div>

            <div class="deductions">
                <h4>Deductions</h4>
                <table>
                    <tr><td>Tax Deducted at Source (T.D.S.)</td><td class="text-right">$0</td></tr>
                    <tr><td>Provident Fund</td><td class="text-right">$0</td></tr>
                    <tr><td>ESI</td><td class="text-right">$0</td></tr>
                    <tr><td>Loan</td><td class="text-right">$300</td></tr>
                    <tr><td><strong>Total Deductions</strong></td><td class="text-right"><strong>$300</strong></td></tr>
                </table>
            </div>
            <div class="clear"></div>
        </div>

        <div class="net-salary">
            Net Salary: $59698 (Fifty nine thousand six hundred and ninety eight only.)
        </div>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("payslip_john_doe.pdf", array("Attachment" => true));
}
?>
