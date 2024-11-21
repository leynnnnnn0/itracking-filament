<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply and Equipment Management System - User's Manual</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1,
        h2,
        h3 {
            color: #333;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px 0;
        }

        .note {
            background-color: #f4f4f4;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <h1>USER'S MANUAL</h1>

    <div class="note">
        <strong>NOTE:</strong> Please fill out:
        <ol>
            <li>management tab</li>
            <li>accountable officer</li>
            <li>personnel</li>
        </ol>
        before proceeding to add supply or equipment
    </div>

    <h2>1. Access Website</h2>
    <h3>1.1 Login</h3>
    <p>Open the website and enter your credentials. Fill out necessary fields and click on the Login button. If you have no account, only the administrator can register you. Ask the admin for registration.</p>
    <img src="{{ Vite::asset('resources/images/loginPage.png')}}" alt="Login Screen" />

    <h2>2. Homepage</h2>
    <h3>2.1 Dashboard</h3>
    <p>In this section, users can view important details such as available equipment types, active borrowed items, missing items, and low-stock supplies.</p>
    <img src="/api/placeholder/600/300" alt="Dashboard" />

    <h3>2.2 Navigation Bar</h3>
    <p>Navigation bar is a central guide for navigating the system's content and functions, providing management options for people, equipment, supplies, and other system needs.</p>
    <img src="/api/placeholder/600/300" alt="Navigation Bar" />

    <h2>3. Audits</h2>
    <p>Audits track and monitor various actions or events within the system. They often provide a record of changes, access, and operations to ensure accountability, security, and compliance.</p>
    <img src="/api/placeholder/600/300" alt="Audits Screen" />

    <h2>4. People</h2>
    <h3>4.1 Accountable Officer</h3>
    <p>An accountable officer list will be displayed, with an option to view full details of each officer by clicking on the three dots.</p>
    <img src="/api/placeholder/600/300" alt="Accountable Officer List" />

    <h4>4.1.1 Adding a New Accountable Officer</h4>
    <p>To add a new accountable officer, click <strong>New Accountable Officer</strong>, fill out the form, and then click <strong>Create</strong>.</p>
    <img src="/api/placeholder/600/300" alt="New Accountable Officer Form" />

    <h3>4.2 Personnel</h3>
    <p>A personnel list will be displayed, with an option to view full details of each personnel by clicking on the three dots.</p>
    <img src="/api/placeholder/600/300" alt="Personnel List" />

    <h4>4.2.1 Adding New Personnel</h4>
    <p>To add new personnel, click <strong>New Personnel</strong>, fill out form and then click <strong>Create</strong>.</p>
    <img src="/api/placeholder/600/300" alt="New Personnel Form" />

    <h3>4.3 Users</h3>
    <p>Users allow you to add an admin or sub-admin to the system.</p>
    <img src="/api/placeholder/600/300" alt="Users Screen" />

    <h4>4.3.1 Adding a New User</h4>
    <p>To add a new user press <strong>New User</strong>.</p>
    <img src="/api/placeholder/600/300" alt="New User Form" />

    <h4>4.3.2 User Roles</h4>
    <p>To determine if the user will be <strong>Admin</strong> or <strong>Sub admin</strong>, select their role on the form.</p>
    <img src="/api/placeholder/600/300" alt="User Role Selection" />

    <h2>5. Management</h2>
    <h3>5.1 Management Navigation</h3>
    <p>In the Management tab, you will find a list of resources required for the dropdowns in the system forms.</p>
    <img src="/api/placeholder/600/300" alt="Management Navigation" />

    <h2>6. Supply</h2>
    <h3>6.1 Supplies</h3>
    <p>The Supplies section will display a list of supplies, with a "View" button to see all the details for each supply.</p>
    <img src="/api/placeholder/600/300" alt="Supplies List" />

    <h4>6.1.1 Adding Quantity or Recording Usage</h4>
    <p>To <strong>add quantity</strong> or <strong>record usage</strong>, press <strong>3 dots</strong>.</p>
    <img src="/api/placeholder/600/300" alt="Supply Quantity Management" />

    <h3>6.2 Supplies Incident</h3>
    <p>The Supplies Incident section will generate a form for you to report missing or expired supplies.</p>
    <img src="/api/placeholder/600/300" alt="Supplies Incident" />

    <h4>6.2.1 Generating a Report</h4>
    <p>To generate a report press <strong>New supply incident</strong>.</p>
    <img src="/api/placeholder/600/300" alt="New Supply Incident Form" />

    <h3>6.3 Supply History</h3>
    <p>The Supply History will show you all changes made to the supplies.</p>
    <img src="/api/placeholder/600/300" alt="Supply History" />

    <h4>6.3.1 Exporting Supply History</h4>
    <p>To export the supply history press <strong>export as PDF</strong>.</p>
    <img src="/api/placeholder/600/300" alt="Export Supply History" />

    <h4>6.3.2 Filtering Supply History</h4>
    <p>To export supplies with a filter, click the <strong>Filter</strong> button and select your preferred filter.</p>
    <img src="/api/placeholder/600/300" alt="Supply History Filter" />

    <h2>7. Equipment</h2>
    <h3>7.1 Equipment</h3>
    <p>The Equipment section will display all equipment along with its availability, borrowing status, missing items, condemned items, and overall status.</p>
    <img src="/api/placeholder/600/300" alt="Equipment List" />

    <h3>7.2 Borrow Equipment</h3>
    <p>The Borrow Equipment section will display a borrow log.</p>
    <img src="/api/placeholder/600/300" alt="Borrow Equipment Log" />

    <h4>7.2.1 Borrowing Equipment</h4>
    <p>There are two ways to borrow equipment: fill out the form and press "submit" to complete the borrowing process.</p>
    <img src="/api/placeholder/600/300" alt="Borrow Equipment Form" />

    <h4>7.2.2 Returning Equipment</h4>
    <p>For the equipment return press 3 dots beside the borrowed equipment logs.</p>
    <img src="/api/placeholder/600/300" alt="Equipment Return" />

    <h2>8. Missing Equipment</h2>
    <h3>8.1 Missing Equipment Log</h3>
    <p>The Missing Equipment section will display a log of missing equipment.</p>
    <img src="/api/placeholder/600/300" alt="Missing Equipment Log" />

    <h4>8.1.1 Updating Missing Equipment Status</h4>
    <p>If the missing equipment is found or reported to SPMO press 3 dots.</p>
    <img src="/api/placeholder/600/300" alt="Update Missing Equipment" />

    <h4>8.1.2 Condemning Equipment</h4>
    <p>Condemning an equipment will require you to report it to SPMO.</p>
    <img src="/api/placeholder/600/300" alt="Condemn Equipment" />

    <h3>8.2 Reporting Missing Equipment</h3>
    <p>To report missing equipment press <strong>new missing equipment</strong>, fill out the form and press "create".</p>
    <img src="/api/placeholder/600/300" alt="Report Missing Equipment" />

    <h3>8.3 Reporting Lost Borrowed Item</h3>
    <p>If a borrower loses an item, go to the Borrower's Log, click the three dots, and select "Report Missing Item." Fill out the form and press "submit." This will generate a report with the borrower's information in the missing log.</p>
    <img src="/api/placeholder/600/300" alt="Report Lost Borrowed Item" />

    <h2>9. Logout</h2>
    <p>To log out, click on the logout option in the system's menu or profile settings. This will securely log you out of the system.</p>
    <img src="/api/placeholder/600/300" alt="Logout Screen" />
</body>

</html>