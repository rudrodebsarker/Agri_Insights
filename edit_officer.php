<?php
session_start();

include 'db_config.php'; 

// Fetch officer data for editing
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];

    // Fetch officer data using prepared statement
    $sql_edit = $conn->prepare("SELECT * FROM agri_officer WHERE officer_id = ?");
    $sql_edit->bind_param('i', $edit_id);
    $sql_edit->execute();
    $result_edit = $sql_edit->get_result();
    $row_edit = $result_edit->fetch_assoc();

    // Fetch contact data associated with the officer
    $sql_contact = $conn->prepare("SELECT contact FROM agri_officer_contact WHERE officer_id = ?");
    $sql_contact->bind_param('i', $edit_id);
    $sql_contact->execute();
    $result_contact = $sql_contact->get_result();
    $contacts = [];
    while ($contact = $result_contact->fetch_assoc()) {
        $contacts[] = $contact['contact'];
    }
    $contacts_str = implode(', ', $contacts); // Join contacts with comma
}

// Update officer details
if (isset($_POST['update_officer'])) {
    $officer_id = $_POST['officer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $road = $_POST['road'];
    $area = $_POST['area'];
    $district = $_POST['district'];
    $country = $_POST['country'];
    $contact = $_POST['contact']; 

    
    $sql_update = $conn->prepare("UPDATE agri_officer 
                                  SET name = ?, email = ?, road = ?, area = ?, district = ?, country = ? 
                                  WHERE officer_id = ?");
    $sql_update->bind_param('ssssssi', $name, $email, $road, $area, $district, $country, $officer_id);
    $sql_update->execute();

    // Update contact details using prepared statement
   
    $sql_contact_update = $conn->prepare("INSERT INTO agri_officer_contact (officer_id, contact) VALUES (?, ?) 
                                         ON DUPLICATE KEY UPDATE contact = ?");
    $sql_contact_update->bind_param('iss', $officer_id, $contact, $contact);
    $sql_contact_update->execute();

    // Redirect back to the list
    header("Location: agriOficers_List.php");
    exit();
}
?>

