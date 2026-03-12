<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #5b3df5;
            padding-bottom: 20px;
        }

        .details {
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f7f6ff;
        }

        .total {
            font-size: 24px;
            font-weight: bold;
            color: #5b3df5;
            text-align: right;
            margin-top: 20px;
        }

        .pay-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #5b3df5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <h2>INVOICE</h2>
            <p>Date: {{ $date }}<br>Invoice #: MS-{{ $milestone->id }}</p>
        </div>

        <div class="details">
            <h3>Invoice Details</h3>
            <p><strong>To (Client):</strong> {{ $client->name ?? 'Customer' }}</p>
            <p><strong>From (Freelancer):</strong> {{ $freelancerName ?? 'Freelancer' }}</p>
            <p><strong>Project:</strong> {{ $milestone->project->name ?? 'Project' }}</p>
            <p><strong>Milestone:</strong> {{ $milestone->title }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Payment for Milestone: {{ $milestone->title }}</td>
                    <td>€{{ number_format($milestone->price, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            Total Due: €{{ number_format($milestone->price, 2) }}
        </div>

        <div style="text-align: center;">
            <p>Click the button below to proceed to the secure Stripe payment gateway:</p>
            <a href="{{ $paymentUrl }}" class="pay-button">Pay Securely via Stripe</a>
        </div>
    </div>
</body>

</html>
