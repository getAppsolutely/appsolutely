<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Form Submission Notification Template for Staff
        NotificationTemplate::updateOrCreate(
            ['slug' => 'form-submission-staff-notification'],
            [
                'name'      => 'Form Submission - Staff Notification',
                'category'  => 'form',
                'subject'   => 'New Form Submission: {{form_name}}',
                'body_html' => $this->getHtmlTemplate(),
                'body_text' => $this->getTextTemplate(),
                'variables' => [
                    'form_name',
                    'form_description',
                    'user_name',
                    'user_email',
                    'user_phone',
                    'submitted_at',
                    'entry_id',
                    'form_data',
                    'admin_link',
                ],
                'is_system' => false,
                'status'    => 1,
            ]
        );
    }

    /**
     * Get HTML email template
     */
    private function getHtmlTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Form Submission</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin-top: 0;">New Form Submission Received</h2>
        <p style="margin: 0; color: #666;">A new form submission has been received and requires your attention.</p>
    </div>

    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 5px; padding: 20px; margin-bottom: 20px;">
        <h3 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #3498db; padding-bottom: 10px;">Form Details</h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 150px;">Form Name:</td>
                <td style="padding: 8px 0;">{{form_name}}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Description:</td>
                <td style="padding: 8px 0;">{{form_description}}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Submitted At:</td>
                <td style="padding: 8px 0;">{{submitted_at}}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Entry ID:</td>
                <td style="padding: 8px 0;">#{{entry_id}}</td>
            </tr>
        </table>
    </div>

    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 5px; padding: 20px; margin-bottom: 20px;">
        <h3 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #3498db; padding-bottom: 10px;">Contact Information</h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 150px;">Name:</td>
                <td style="padding: 8px 0;">{{user_name}}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Email:</td>
                <td style="padding: 8px 0;"><a href="mailto:{{user_email}}" style="color: #3498db; text-decoration: none;">{{user_email}}</a></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Phone:</td>
                <td style="padding: 8px 0;"><a href="tel:{{user_phone}}" style="color: #3498db; text-decoration: none;">{{user_phone}}</a></td>
            </tr>
        </table>
    </div>

    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 5px; padding: 20px; margin-bottom: 20px;">
        <h3 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #3498db; padding-bottom: 10px;">Form Data</h3>
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 3px; font-family: monospace; white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto;">{{form_data}}</div>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
        <a href="{{admin_link}}" style="display: inline-block; background-color: #3498db; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">View in Admin Panel</a>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #999; font-size: 12px;">
        <p>This is an automated notification. Please do not reply to this email.</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get plain text email template
     */
    private function getTextTemplate(): string
    {
        return <<<'TEXT'
NEW FORM SUBMISSION RECEIVED
============================

A new form submission has been received and requires your attention.

FORM DETAILS
------------
Form Name: {{form_name}}
Description: {{form_description}}
Submitted At: {{submitted_at}}
Entry ID: #{{entry_id}}

CONTACT INFORMATION
-------------------
Name: {{user_name}}
Email: {{user_email}}
Phone: {{user_phone}}

FORM DATA
---------
{{form_data}}

VIEW IN ADMIN PANEL
-------------------
{{admin_link}}

---
This is an automated notification. Please do not reply to this email.
TEXT;
    }
}
