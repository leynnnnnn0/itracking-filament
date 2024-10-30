<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Print Table</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding: 20px;
            color: #333;
        }

        .center {
            text-align: center;
            margin: 0;
        }

        section {
            width: 100%;
            display: table;
            margin-bottom: 15px;
        }

        @page {
            margin: 0.5cm;
        }

        /* Table styles */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
            table-layout: fixed;
            background-color: white;
        }

        .print-table th {
            background-color: #0B592D;
            color: white;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #0B592D;
            padding: 8px;
            text-align: left;
        }

        .print-table td {
            font-size: 11px;
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Alternate row colors */
        .print-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .print-table tbody tr:hover {
            background-color: #f1f5f3;
        }

        /* Ensure rows don't break across pages */
        .print-table tr {
            page-break-inside: avoid;
        }

        /* Footer row styles */
        .print-table tfoot tr {
            font-weight: 600;
            background-color: #f2f7f5;
            border-top: 2px solid #0B592D;
        }

        /* Numeric alignment */
        .print-table .numeric {
            text-align: right;
        }

        /* Status cell styling */
        .print-table .status {
            text-align: center;
            font-weight: 500;
        }

        /* Table header section */
        .table-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #0B592D;
            padding-bottom: 10px;
        }

        .table-header h1 {
            color: #0B592D;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .table-header p {
            color: #666;
            font-size: 12px;
        }

        /* Table footer section */
        .table-footer {
            margin-top: 20px;
            font-size: 11px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Custom column widths */
        .print-table .col-small {
            width: 8%;
        }

        .print-table .col-medium {
            width: 15%;
        }

        .print-table .col-large {
            width: 25%;
        }

        /* Additional utility classes */
        .text-bold {
            font-weight: 600;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Print-specific styles */
        @media print {
            body {
                padding: 0;
            }

            .print-table {
                page-break-inside: auto;
            }

            .print-table thead {
                display: table-header-group;
            }

            .print-table tfoot {
                display: table-footer-group;
            }

            .table-header, .table-footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    {{ $slot }}

    <div class="table-footer">
        <p>This is an official document generated from the iTrack System</p>
    </div>
</body>
</html>