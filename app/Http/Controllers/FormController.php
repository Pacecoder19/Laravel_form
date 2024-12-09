<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{
    public function upload(Request $request)
{
    // Custom error messages for validation
    $messages = [
        'name.required' => 'The name field is required.',
        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'email.unique' => 'The email has already been taken.',
        'phone.required' => 'The phone number is required.',
        'phone.regex' => 'The phone number must be a valid Indian phone number (starting with +91 or 91).',
        'description.required' => 'The description field is required.',
        'profile_photo.required' => 'The profile photo is required.',
        'profile_photo.image' => 'The profile photo must be an image.',
        'profile_photo.mimes' => 'The profile photo must be a JPG, JPEG, PNG, or GIF image.',
        'profile_photo.max' => 'The profile photo must not be greater than 2MB.',
    ];

    // Server-side validation
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
        'phone' => ['required', 'regex:/^(\+91|91)?[789]\d{9}$/'],
        'description' => 'required|string',
        'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
    ], $messages);

    // Handle file upload and data saving as before
    $photoPath = null;
    if ($request->hasFile('profile_photo')) {
        $profilePhoto = $request->file('profile_photo');
        $photoPath = $profilePhoto->store('profile_photos', 'public');
    }

    // Save data to the database
    $data = new User;
    $data->name = $validated['name'];
    $data->email = $validated['email'];
    $data->phone = $validated['phone'];
    $data->description = $validated['description'];
    $data->profile_photo = $photoPath;
    $data->save();

    // Return a success response with the data and success message
    return response()->json([
        'message' => 'Data uploaded successfully!',
        'data' => [
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'description' => $data->description,
            'profile_photo' => asset('storage/' . $data->profile_photo),
        ]
    ]);
}
    public function getUsers()
{
    $users = User::all(); // Get all users from the database

    // Return the users as a JSON response
    return response()->json($users);
}

}
