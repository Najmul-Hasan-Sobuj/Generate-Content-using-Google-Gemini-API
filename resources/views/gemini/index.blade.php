<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Gemini API - Laravel 11</title>
    <!-- MDBootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- PrismJS CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
    <!-- Custom CSS for Code Styling -->
    <style>
        pre {
            background: #f5f5f5;
            padding: 1em;
            border-radius: 5px;
            overflow-x: auto;
            margin-bottom: 1.5em;
            border-left: 4px solid #007bff;
        }

        .edit-prompt {
            border: none;
            background-color: transparent;
            width: 100%;
            font-size: 1em;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            outline: none;
            color: #333;
        }

        .edit-prompt:focus {
            background-color: #f8f9fa;
        }

        footer {
            background-color: #343a40;
            color: #ffffff;
            padding: 1em 0;
            text-align: center;
            margin-top: 50px;
            border-top: 5px solid #007bff;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h3 class="card-title text-center">Generate Content using Google Gemini API</h3>

                        <div id="generated-content" class="mt-4">
                            <!-- The generated content with editable prompts will be displayed here -->
                        </div>

                        <div id="loader" class="text-center" style="display:none;">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <form id="gemini-form" class="mt-4">
                            <div class="form-outline mb-4">
                                <input type="text" id="prompt" name="prompt" class="form-control" />
                                <label class="form-label" for="prompt">Enter your prompt</label>
                            </div>
                            <div class="text-center">
                                <button type="submit" id="generate-button" class="btn btn-primary btn-block">Generate</button>
                                <button type="button" id="clear-button" class="btn btn-danger btn-block mt-2">Clear</button>
                                <button type="button" id="new-chat-button" class="btn btn-secondary btn-block mt-2">New Chat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 <a href="mailto:najmulhasansobuj87@gmail.com">Najmul Hasan</a></p>
    </footer>

    <!-- MDBootstrap JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- PrismJS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-java.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#gemini-form').on('submit', function(e) {
                e.preventDefault();

                const prompt = $('#prompt').val();

                // Check if the input is empty
                if (!prompt) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Prompt',
                        text: 'Please enter a prompt before generating.',
                    });
                    return; // Exit if prompt is empty
                }

                // Disable the button and show loader
                $('#generate-button').prop('disabled', true);
                $('#loader').show();

                $.ajax({
                    url: '{{ route('gemini.generate') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prompt: prompt
                    },
                    success: function(response) {
                        // Get the current date and time
                        const now = new Date();
                        const dateString = now.toLocaleDateString(); // Format date
                        const timeString = now.toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }); // Format time (12-hour format)

                        // Format the generated text properly
                        const formattedText = response.generatedText
                            .replace(/```python([\s\S]*?)```/g, '<pre><code class="language-python">$1</code></pre>')
                            .replace(/```java([\s\S]*?)```/g, '<pre><code class="language-java">$1</code></pre>')
                            .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
                            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold text between **
                            .replace(/\* (.*?)(?=\n|$)/g, '<li>$1</li>') // List items
                            .replace(/\n/g, '<br>'); // Replace new lines with <br> for proper line breaks

                        // Create a new section for the generated content with timestamp and editable prompt
                        const contentSection = `
                            <div class="alert alert-light mt-3">
                                <strong>${dateString} ${timeString}</strong>
                                <div>
                                    <input type="text" class="edit-prompt" value="${prompt}">
                                </div>
                                <div>${formattedText}</div>
                            </div>
                        `;
                        $('#generated-content').append(contentSection).show();

                        // Clear the input field after generating content
                        $('#prompt').val('');
                    },
                    error: function() {
                        $('#generated-content').append(
                            '<div class="alert alert-danger mt-3">An error occurred while generating content.</div>'
                        );
                    },
                    complete: function() {
                        // Re-enable the button and hide loader
                        $('#generate-button').prop('disabled', false);
                        $('#loader').hide();
                    }
                });
            });

            // Clear button functionality
            $('#clear-button').on('click', function() {
                $('#generated-content').empty(); // Clear previous conversation
            });

            // New Chat button functionality
            $('#new-chat-button').on('click', function() {
                // Clear the input and previous conversation
                $('#prompt').val('');
                $('#generated-content').empty();
            });
        });
    </script>
</body>

</html>
