try {
    $user = App\Models\User::create([
        'name' => 'Test Google',
        'email' => 'test@google.com',
        'password' => 'password',
        'google_id' => '123456789',
        'is_active' => true,
    ]);
    echo "Success: User ID " . $user->id;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
exit();
