<?php
/**
 * Update Profile API
 * Handles profile updates for both clients and fundis
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Ensure user is logged in
requireLogin();

$response = ['success' => false, 'message' => 'An error occurred'];

try {
    $userId = getCurrentUser()['id'];
    $userRole = $_SESSION['role'];

    // Start transaction
    $db->beginTransaction();

    // Update general user profile
    if (!empty($_POST['full_name']) || !empty($_POST['phone']) || !empty($_POST['location'])) {
        $updateFields = [];
        $updateParams = [];

        if (!empty($_POST['full_name'])) {
            $updateFields[] = 'full_name = ?';
            $updateParams[] = sanitize($_POST['full_name']);
        }

        if (!empty($_POST['phone'])) {
            $updateFields[] = 'phone = ?';
            $updateParams[] = sanitize($_POST['phone']);
        }

        if (!empty($_POST['location'])) {
            $updateFields[] = 'location = ?';
            $updateParams[] = sanitize($_POST['location']);
        }

        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
            try {
                $uploadedFile = uploadFile($_FILES['profile_image'], 'profile', 2 * 1024 * 1024); // 2MB limit
                if ($uploadedFile) {
                    $updateFields[] = 'profile_image = ?';
                    $updateParams[] = $uploadedFile;
                }
            } catch (Exception $e) {
                throw new Exception('Profile image upload failed: ' . $e->getMessage());
            }
        }

        if (!empty($updateFields)) {
            $updateParams[] = $userId;
            $updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->execute($updateParams);
        }
    }

    // Update fundi-specific profile (if user is a fundi)
    if ($userRole === ROLE_FUNDI) {
        $fundiUpdateFields = [];
        $fundiUpdateParams = [];

        if (!empty($_POST['service_category'])) {
            $fundiUpdateFields[] = 'service_category = ?';
            $fundiUpdateParams[] = sanitize($_POST['service_category']);
        }

        if (!empty($_POST['bio'])) {
            $fundiUpdateFields[] = 'bio = ?';
            $fundiUpdateParams[] = sanitize($_POST['bio']);
        }

        if (!empty($_POST['hourly_rate'])) {
            $fundiUpdateFields[] = 'hourly_rate = ?';
            $fundiUpdateParams[] = floatval($_POST['hourly_rate']);
        }

        if (!empty($_POST['years_experience'])) {
            $fundiUpdateFields[] = 'experience_years = ?';
            $fundiUpdateParams[] = intval($_POST['years_experience']);
        }

        if (!empty($_POST['location'])) {
            $fundiUpdateFields[] = 'location = ?';
            $fundiUpdateParams[] = sanitize($_POST['location']);
        }

        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['size'] > 0) {
            try {
                $uploadedFile = uploadFile($_FILES['cover_image'], 'profile', 5 * 1024 * 1024); // 5MB limit
                if ($uploadedFile) {
                    $fundiUpdateFields[] = 'cover_image = ?';
                    $fundiUpdateParams[] = $uploadedFile;
                }
            } catch (Exception $e) {
                throw new Exception('Cover image upload failed: ' . $e->getMessage());
            }
        }

        if (!empty($fundiUpdateFields)) {
            // Get fundi profile ID
            $stmt = $db->prepare("SELECT id FROM fundi_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $fundiProfile = $stmt->fetch();

            if ($fundiProfile) {
                $fundiUpdateParams[] = $fundiProfile['id'];
                $updateQuery = "UPDATE fundi_profiles SET " . implode(', ', $fundiUpdateFields) . " WHERE id = ?";
                $stmt = $db->prepare($updateQuery);
                $stmt->execute($fundiUpdateParams);
            }
        }
    }

    // Update client-specific profile (if user is a client)
    if ($userRole === ROLE_CLIENT) {
        // Additional client profile fields can be added here
        // Currently handled by general user profile update
    }

    // Commit transaction
    $db->commit();

    $response = [
        'success' => true,
        'message' => 'Profile updated successfully'
    ];

} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    http_response_code(400);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
