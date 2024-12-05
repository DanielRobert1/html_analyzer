<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

it('uploads an HTML file and returns accessibility issues', function () {
    // Step 1: Prepare a sample HTML file with accessibility issues
    $htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Page</title>
</head>
<body>
    <form>
        <input type="text" id="username">
    </form>
</body>
</html>
HTML;

    // Step 2: Save the HTML content to a file
    Storage::fake('local');
    $file = UploadedFile::fake()->createWithContent('test.html', $htmlContent);

    // Step 3: Make a POST request to the endpoint
    $response = $this->postJson(route('api.accessibility.analyze'), [
        'file' => $file,
    ]);

    // Step 4: Assert the response status and check for accessibility issues
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'message',
        'score',
    ]);

    // Check that the message is correct
    $response->assertJson([
        'status' => 'success',
        'message' => 'File analyzed successfully!',
    ]);
});
