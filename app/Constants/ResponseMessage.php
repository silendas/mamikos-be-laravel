<?php

namespace App\Constants;

class ResponseMessage {
    public const REGISTER_SUCCESS = "User registered successfully";
    public const LOGIN_SUCCESS = "User logged in successfully";
    public const KOST_CREATED = "Kost created successfully";
    public const KOST_UPDATED = "Kost updated successfully";
    public const KOST_DELETED = "Kost deleted successfully";
    public const KOST_FETCHED = "Kost fetched successfully";
    public const INQUIRY_SUCCESS = "Room availability inquiry sent successfully";
    public const CREDITS_RECHARGED = "User credits recharged successfully";
    public const USER_PROFILE_FETCHED = "User profile fetched successfully";
    public const USER_PROFILE_UPDATED = "User profile updated successfully";
    public const PASSWORD_CHANGED = "Password changed successfully";
    
    public const USER_NOT_FOUND = "User not found";
    public const KOST_NOT_FOUND = "Kost not found";
    public const UNAUTHORIZED = "Unauthorized access";
    public const INSUFFICIENT_CREDITS = "Insufficient credits for inquiry (requires 5 credits)";
    public const EMAIL_ALREADY_EXISTS = "Email already exists";
    public const USERNAME_ALREADY_EXISTS = "Username already exists";
    public const INQUIRIES_FETCHED = "Inquiries fetched successfully";
    public const ONLY_OWNERS_CAN_ADD_KOSTS = "Only owners can add kosts";
    public const NOT_KOST_OWNER = "You are not the owner of this kost";
    public const INCORRECT_CURRENT_PASSWORD = "Incorrect current password";
}
