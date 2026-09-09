
<?php

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL)
        ? null
        : "Enter a valid email address.";
}


function validateRequired(string $value, string $label): ?string
{
    return trim($value) === ''
        ? "$label is required."
        : null;
}


function validateUsernameFormat(string $value): ?string
{
    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $value)) {
        return "Username must be 3-50 characters and can only contain letters, numbers, and underscores.";
    }

    return null;
}


function validatePasswordStrength(string $value): ?string
{
    if (strlen($value) < 8) {
        return "Password must be at least 8 characters long.";
    }

    if (!preg_match('/[a-z]/', $value)) {
        return "Password must contain at least one lowercase letter.";
    }

    if (!preg_match('/[0-9]/', $value)) {
        return "Password must contain at least one number.";
    }

    return null;
}


function validatePasswordMatch(
    string $password,
    string $confirmPassword
): ?string {
    return $password !== $confirmPassword
        ? "Passwords do not match."
        : null;
}


function validatePhoneFormat(string $value): ?string
{
    if ($value === '') {
        return null;
    }

    if (!preg_match('/^[0-9+\-\s]{7,20}$/', $value)) {
        return "Enter a valid phone number.";
    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Registration Validation
|--------------------------------------------------------------------------
*/

function validateRegisterInput(array $post): array
{
    $username = trim($post['username'] ?? '');
    $fullName = trim($post['full_name'] ?? '');
    $email = trim($post['email'] ?? '');
    $phone = trim($post['phone'] ?? '');
    $password = $post['password'] ?? '';
    $confirmPassword = $post['confirm_password'] ?? '';

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateUsernameFormat($username),

        validateRequired($fullName, 'Full name'),

        validateRequired($email, 'Email'),
        validateEmailFormat($email),

        validatePhoneFormat($phone),

        validateRequired($password, 'Password'),
        validatePasswordStrength($password),

        validateRequired($confirmPassword, 'Confirm password'),
        validatePasswordMatch($password, $confirmPassword),
    ]);

    $errors = array_values($errors);

    return [
        'errors' => $errors,

        'data' => [
            'username' => $username,
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'confirm_password' => $confirmPassword,
        ],
    ];
}


/*
|--------------------------------------------------------------------------
| Login Validation
|--------------------------------------------------------------------------
*/

function validateLoginInput(array $post): array
{
    $username = trim($post['username'] ?? '');
    $password = $post['password'] ?? '';

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateRequired($password, 'Password'),
    ]);

    $errors = array_values($errors);

    return [
        'errors' => $errors,

        'data' => [
            'username' => $username,
            'password' => $password,
        ],
    ];
}

function validateContactName(string $value): ?string
{
    if (trim($value) === '') {
        return "Name is required.";
    }

    if (strlen(trim($value)) < 2) {
        return "Name must be at least 2 characters long.";
    }

    return null;
}

function validateContactEmail(string $value): ?string
{
    if (trim($value) === '') {
        return "Email is required.";
    }

    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return "Enter a valid email address.";
    }

    return null;
}

function validateContactRole(string $value): ?string
{
    $allowedRoles = [
        'PLAYER/COURT OWNER',
        'PLAYER',
        'COURT OWNER'
    ];

    if (!in_array($value, $allowedRoles, true)) {
        return "Please select a valid role.";
    }

    return null;
}

function validateContactMessage(string $value): ?string
{
    if (trim($value) === '') {
        return "Message is required.";
    }

    if (strlen(trim($value)) < 5) {
        return "Message must be at least 5 characters long.";
    }

    return null;
}

function validateContactInput(array $post): array
{
    $name = trim($post['name'] ?? '');
    $email = trim($post['email'] ?? '');
    $role = trim($post['role'] ?? '');
    $message = trim($post['message'] ?? '');

    $errors = array_filter([
        validateContactName($name),
        validateContactEmail($email),
        validateContactRole($role),
        validateContactMessage($message),
    ]);

    return [
        'errors' => array_values($errors),
        'data' => [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'message' => $message,
        ],
    ];
}