<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Widget</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --success: #10b981;
            --error: #ef4444;
            --bg: #fafafa;
            --surface: #ffffff;
            --text: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .widget-container {
            width: 100%;
            max-width: 480px;
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .widget-header {
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .widget-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .widget-header p {
            opacity: 0.9;
            font-size: 0.875rem;
        }

        .widget-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            font-size: 0.875rem;
            color: var(--text);
        }

        input, textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.9375rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        .btn {
            width: 100%;
            padding: 14px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .message {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.875rem;
            display: none;
        }

        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            display: block;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            display: block;
        }

        .error-list {
            margin-top: 8px;
            padding-left: 20px;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .field-error {
            color: var(--error);
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .required::after {
            content: ' *';
            color: var(--error);
        }
    </style>
</head>
<body>
    <div class="widget-container">
        <div class="widget-header">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you</p>
        </div>

        <div class="widget-body">
            <div id="successMessage" class="message"></div>
            <div id="errorMessage" class="message"></div>

            <form id="feedbackForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="required">Your Name</label>
                    <input type="text" id="name" name="name" required placeholder="John Doe">
                    <div class="field-error" id="name-error"></div>
                </div>

                <div class="form-group">
                    <label for="email" class="required">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="john@example.com">
                    <div class="field-error" id="email-error"></div>
                </div>

                <div class="form-group">
                    <label for="phone" class="required">Phone Number (E.164)</label>
                    <input type="tel" id="phone" name="phone" required placeholder="+1234567890">
                    <div class="field-error" id="phone-error"></div>
                </div>

                <div class="form-group">
                    <label for="subject" class="required">Subject</label>
                    <input type="text" id="subject" name="subject" required placeholder="How can we help?">
                    <div class="field-error" id="subject-error"></div>
                </div>

                <div class="form-group">
                    <label for="text" class="required">Message</label>
                    <textarea id="text" name="text" required placeholder="Describe your issue or question..."></textarea>
                    <div class="field-error" id="text-error"></div>
                </div>

                <div class="form-group">
                    <label for="files">Attachments (optional)</label>
                    <input type="file" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <div class="field-error" id="files-error"></div>
                </div>

                <button type="submit" class="btn" id="submitBtn">
                    Send Message
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('feedbackForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = document.getElementById('submitBtn');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
            successMessage.className = 'message';
            errorMessage.className = 'message';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading"></span>Sending...';

            const formData = new FormData(form);

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    successMessage.textContent = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
                    successMessage.className = 'message success';
                    form.reset();
                } else if (response.status === 422) {
                    errorMessage.innerHTML = '<strong>Please fix the following errors:</strong>';
                    const errorList = document.createElement('ul');
                    errorList.className = 'error-list';

                    for (const [field, messages] of Object.entries(data.errors || {})) {
                        const fieldError = document.getElementById(`${field}-error`);
                        if (fieldError) {
                            fieldError.textContent = messages[0];
                        }
                        messages.forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                    }

                    errorMessage.appendChild(errorList);
                    errorMessage.className = 'message error';
                } else if (response.status === 429) {
                    errorMessage.textContent = data.message || 'You can only submit one request per day.';
                    errorMessage.className = 'message error';
                } else {
                    errorMessage.textContent = data.message || 'Something went wrong. Please try again.';
                    errorMessage.className = 'message error';
                }
            } catch (err) {
                errorMessage.textContent = 'Network error. Please check your connection and try again.';
                errorMessage.className = 'message error';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Send Message';
            }
        });
    </script>
</body>
</html>
