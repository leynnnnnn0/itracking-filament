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
    <img src="{{ Vite::asset('resources/images/Dashboard.png')}}" alt="Dashboard" />

    <h3>2.2 Navigation Bar</h3>
    <p>Navigation bar is a central guide for navigating the system's content and functions, providing management options for people, equipment, supplies, and other system needs.</p>
    <img src="{{ Vite::asset('resources/images/Navigation Bar.png')}}" alt="Navigation Bar" />

    <h2>3. Audits</h2>0
    <p>Audits track and monitor various actions or events within the system. They often provide a record of changes, access, and operations to ensure accountability, security, and compliance.</p>
    <img src="{{ Vite::asset('resources/images/Audits.png')}}" alt="Audits" />

    <h2>4. People</h2>
    <h3>4.1 Accountable Officer</h3>
    <p>An accountable officer list will be displayed, with an option to view full details of each officer by clicking on the three dots.</p>
    <img src="{{ Vite::asset('resources/images/Accountable Officer.png')}}" alt="Accountable Officer" />

    <h4>4.1.1 Adding a New Accountable Officer</h4>
    <p>To add a new accountable officer, click <strong>New Accountable Officer</strong>, fill out the form, and then click <strong>Create</strong>.</p>
    <img src="{{ Vite::asset('resources/images/New Accountable Officer.png')}}" alt="New Accountable Officer" />
    <img src="{{ Vite::asset('resources/images/New Accountable Officer Form.png')}}" alt="New Accountable Officer Form" />

    <h3>4.2 Personnel</h3>
    <p>A personnel list will be displayed, with an option to view full details of each personnel by clicking on the three dots.</p>
    <img src="{{ Vite::asset('resources/images/Personnel.png')}}" alt="Personnel" />

    <h4>4.2.1 Adding New Personnel</h4>
    <p>To add new personnel, click <strong>New Personnel</strong>, fill out form and then click <strong>Create</strong>.</p>
    <img src="{{ Vite::asset('resources/images/New_personnel.png')}}" alt="New_personnel" />
    <img src="{{ Vite::asset('resources/images/Personnel_form.png')}}" alt="Personnel_form" />

    <h3>4.3 Users</h3>
    <p>Users allow you to add an admin or sub-admin to the system.</p>
    <img src="{{ Vite::asset('resources/images/User.png')}}" alt="User" />

    <h4>4.3.1 Adding a New User</h4>
    <p>To add a new user press <strong>New User</strong>, fill out form and then click <strong>Create</strong>.</p>
    <img src="{{ Vite::asset('resources/images/New_User.png')}}" alt="New_User" />
    <img src="{{ Vite::asset('resources/images/User_Form.png')}}" alt="User_Form" />

    <h4>4.3.2 User Roles</h4>
    <p>To determine if the user will be <strong>Admin</strong> or <strong>Sub admin</strong>, select their role on the form.</p>
    <img src="{{ Vite::asset('resources/images/User_Role.png')}}" alt="User_Role" />

    <h2>5. Management</h2>
    <h3>5.1 Management Navigation</h3>
    <p>In the Management tab, you will find a list of resources required for the dropdowns in the system forms.</p>
    <img src="{{ Vite::asset('resources/images/Management.png')}}" alt="Management" />
    <p>To add a new press <strong>New </strong>, fill out form and then click <strong>Create</strong>.</p>
    <img src="{{ Vite::asset('resources/images/New_Management.png')}}" alt="New_Management" />
    <img src="{{ Vite::asset('resources/images/Management_Form.png')}}" alt="Management_Form" />

    <h2>6. Supply</h2>
    <h3>6.1 Supplies</h3>
    <p>The Supplies section will display a list of supplies, with a "View" button to see all the details for each supply.</p>
    <img src="{{ Vite::asset('resources/images/Supply.png')}}" alt="Supply" />
    <p>To add a new supply press <strong>New Supply</strong>, fill out form and then click <strong>Create</strong>.</p>
    <img src="{{ Vite::asset('resources/images/New_Supply.png')}}" alt="New_Supply" />
    <img src="{{ Vite::asset('resources/images/Supply_Form.png')}}" alt="Supply_Form" />

    <h4>6.1.1 Adding Quantity or Recording Usage</h4>
    <p>To <strong>add quantity</strong> or <strong>record usage</strong>, press <strong>3 dots</strong>.</p>
    <img src="{{ Vite::asset('resources/images/Supply_Update.png')}}" alt="Supply_Update" />
    <p>Fill out form and then click <strong>Confirm</strong>.</p>
    <img src="{{ Vite::asset('resources/images/Supply_Update_Form.png')}}" alt="Supply_Update_Form" />

    <h3>6.2 Supplies Incident</h3>
    <p>The Supplies Incident section will generate a form for you to report missing or expired supplies.</p>
    <img src="{{ Vite::asset('resources/images/Incident.png')}}" alt="Incident" />

    <h4>6.2.1 Generating a Report</h4>
    <p>To generate a report press <strong>New supply incident</strong>.</p>
    <img src="{{ Vite::asset('resources/images/New_Incident.png')}}" alt="Incident" />
    <img src="{{ Vite::asset('resources/images/Incident_Form.png')}}" alt="Incident_Form" />

    <h3>6.3 Supply History</h3>
    <p>The Supply History will show you all changes made to the supplies.</p>
    <img src="{{ Vite::asset('resources/images/History.png')}}" alt="History" />

    <h4>6.3.1 Exporting Supply History</h4>
    <p>To export the supply history press <strong>export as PDF</strong>.</p>
    <img src="{{ Vite::asset('resources/images/History_Export.png')}}" alt="History_Export" />

    <h4>6.3.2 Filtering Supply History</h4>
    <p>To export supplies with a filter, click the <strong>Filter</strong> button and select your preferred filter.</p>
    <img src="{{ Vite::asset('resources/images/History_Filter.png')}}" alt="History_Filter" />
    <h2>7. Equipment</h2>
    <h3>7.1 Equipment</h3>
    <p>The Equipment section will display all equipment along with its availability, borrowing status, missing items, condemned items, and overall status.</p>
    <img src="{{ Vite::asset('resources/images/Equipment.png')}}" alt="Equipment" />

    <h3>7.2 Borrow Equipment</h3>
    <p>The Borrow Equipment section will display a borrow log.</p>
    <img src="{{ Vite::asset('resources/images/Borrow.png')}}" alt="Borrow" />

    <h4>7.2.1 Borrowing Equipment</h4>
    <p>There are two ways to borrow equipment: fill out the form and press "Create" to complete the borrowing process.</p>
    <img src="{{ Vite::asset('resources/images/Borrow1.png')}}" alt="Borrow1" />
    <img src="{{ Vite::asset('resources/images/Borrow2.png')}}" alt="Borrow2" />
    <img src="{{ Vite::asset('resources/images/Borrow_Form.png')}}" alt="Borrow_Form" />


    <h4>7.2.2 Returning Equipment</h4>
    <p>For the equipment return press 3 dots beside the borrowed equipment logs.</p>
    <img src="{{ Vite::asset('resources/images/Borrow_Return.png')}}" alt="Borrow_Return" />

    <h2>8. Missing Equipment</h2>
    <h3>8.1 Missing Equipment Log</h3>
    <p>The Missing Equipment section will display a log of missing equipment.</p>
    <img src="{{ Vite::asset('resources/images/Missing.png')}}" alt="Missing" />

    <h3>8.2 Reporting Missing Equipment</h3>
    <p>To report missing equipment press <strong>new missing equipment</strong>, fill out the form and press "create".</p>
    <img src="{{ Vite::asset('resources/images/New_Missing.png')}}" alt="New_Missing" />
    <img src="{{ Vite::asset('resources/images/Missing_Form.png')}}" alt="Missing_Form" />


    <h4>8.2.1 Updating Missing Equipment Status</h4>
    <p>If the missing equipment is found or reported to SPMO press 3 dots.</p>
    <img src="{{ Vite::asset('resources/images/Missing_Found.png')}}" alt="Missing_Found" />
    <img src="{{ Vite::asset('resources/images/Missing_SPMO.png')}}" alt="Missing_SPMO" />

    <h4>8.2.2 Condemning Equipment</h4>
    <p>Condemning an equipment will require you to report it to SPMO.</p>
    <img src="{{ Vite::asset('resources/images/Missing_Status.png')}}" alt="Missing_Status" />
    <img src="{{ Vite::asset('resources/images/Missing_Condemn.png')}}" alt="Missing_Condemn" />
 
    <h3>8.3 Reporting Lost Borrowed Item</h3>
    <p>If a borrower loses an item, go to the Borrower's Log, click the three dots, and select "Report Missing Item." Fill out the form and press "submit." This will generate a report with the borrower's information in the missing log.</p>
    <img src="{{ Vite::asset('resources/images/Missing_Borrow.png')}}" alt="Missing_Borrow" />

    <h2>9. Notification</h2>
    <p>This will show you notifications like expiry, contract and low supply.</p>
    <img src="{{ Vite::asset('resources/images/Notification.png')}}" alt="Notification" />
    <img src="{{ Vite::asset('resources/images/Notification_List.png')}}" alt="Notification_List" />

    <h2>9. Logout</h2>
    <p>To log out, click on the logout option in the system's menu or profile settings. This will securely log you out of the system.</p>
    <img src="{{ Vite::asset('resources/images/Logout.png')}}" alt="Logout" />
</body>

</html>