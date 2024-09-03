<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$success = $error = '';
$editMode = false;
$editProgram = null;

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = $_POST['program_name'];
    $description = $_POST['description'];

    if (isset($_POST['program_id'])) {
        // Update existing program
        $program_id = $_POST['program_id'];
        try {
            $stmt = $pdo->prepare('UPDATE programs SET program_name = ?, description = ? WHERE program_id = ?');
            $stmt->execute([$program_name, $description, $program_id]);
            $success = 'Program updated successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to update program: ' . $e->getMessage();
        }
    } else {
        // Insert new program
        try {
            $stmt = $pdo->prepare('INSERT INTO programs (program_name, description) VALUES (?, ?)');
            $stmt->execute([$program_name, $description]);
            $success = 'Program added successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to add program: ' . $e->getMessage();
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $program_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM programs WHERE program_id = ?');
        $stmt->execute([$program_id]);
        $success = 'Program deleted successfully!';
    } catch (PDOException $e) {
        $error = 'Failed to delete program: ' . $e->getMessage();
    }
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $program_id = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM programs WHERE program_id = ?');
    $stmt->execute([$program_id]);
    $editProgram = $stmt->fetch();
    if ($editProgram) {
        $editMode = true;
    }
}

// Fetch all programs
$stmt = $pdo->prepare('SELECT * FROM programs');
$stmt->execute();
$programs = $stmt->fetchAll();
