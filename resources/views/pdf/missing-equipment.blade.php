<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missing Equipment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.3;
            color: #333;
            margin: 20px;
            font-size: 12px;
            position: relative;
            min-height: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #0B592D;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0B592D;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #ccc;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 4px 6px;
            font-weight: bold;
            background-color: #f3f4f6;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            padding: 4px 6px;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .content-wrapper {
            margin-bottom: 50px;
            /* Space for footer */
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ddd;
            background-color: white;
        }

        @page {
            margin: 20px;
            margin-bottom: 40px;
            /* Extra bottom margin for footer */
        }
    </style>
</head>

<body>
    <div class="content-wrapper">
        <div class="header">
            <h1>Missing Equipment Report</h1>
            <p>Generated on: {{ date('F d, Y') }}</p>
        </div>

        <div class="section">
            <div class="section-title">Equipment Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Equipment Id</div>
                    <div class="info-value">{{ $equipment->equipment->id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Equipment</div>
                    <div class="info-value">{{ $equipment->equipment->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Property Number</div>
                    <div class="info-value">{{ $equipment->equipment->property_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Description</div>
                    <div class="info-value">{{ $equipment->equipment->description }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Equipment Accountable Officer</div>
                    <div class="info-value">{{ $equipment->equipment->accountable_officer->full_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Equipment Responsible Person</div>
                    <div class="info-value">{{ $equipment->equipment->personnel->full_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Organization Unit</div>
                    <div class="info-value">{{ $equipment->equipment->organization_unit->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Operating Unit/Project</div>
                    <div class="info-value">{{ $equipment->equipment->operating_unit_project->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fund</div>
                    <div class="info-value">{{ $equipment->equipment->fund->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Unit</div>
                    <div class="info-value">{{ $equipment->equipment->unit }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Unit Price</div>
                    <div class="info-value">{{ $equipment->equipment->total_amount }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Amount</div>
                    <div class="info-value">{{ $equipment->equipment->unit_price }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity</div>
                    <div class="info-value">{{ $equipment->equipment->quantity }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity Available</div>
                    <div class="info-value">{{ $equipment->equipment->quantity_available }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity Borrowed</div>
                    <div class="info-value">{{ $equipment->equipment->quantity_borrowed }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity Missing</div>
                    <div class="info-value">{{ $equipment->equipment->quantity_missing }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity Condemend</div>
                    <div class="info-value">{{ $equipment->equipment->quantity_condemned }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date Acquired</div>
                    <div class="info-value">{{ $equipment->equipment->date_acquired->format('F d, Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estimated Useful Time</div>
                    <div class="info-value">{{ $equipment->estimated_useful_time ? 'Until ' .  Carbon\Carbon::parse($equipment->estimated_useful_time)->format('F Y') : 'N/a' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ $equipment->equipment->status }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Report Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Report Id</div>
                    <div class="info-value">{{ $equipment->id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity Missing</div>
                    <div class="info-value">{{ $equipment->quantity }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Quantity Found</div>
                    <div class="info-value">{{ $equipment->quantity_found ?? 0 }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ $equipment->status }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Reported By</div>
                    <div class="info-value">{{ $equipment->reported_by }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Reported Date</div>
                    <div class="info-value">{{ $equipment->reported_date->format('F d, Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Description</div>
                    <div class="info-value">{{ $equipment->description ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Is Condemned</div>
                    <div class="info-value">{{ $equipment->is_condemned ? 'Yes' : 'No' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is an official document generated from the iTracking</p>
    </div>
</body>

</html>