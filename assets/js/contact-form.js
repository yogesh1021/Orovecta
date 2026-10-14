document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form.php-email-form");

    forms.forEach(function (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            const loading = form.querySelector(".loading");
            const errorMessage = form.querySelector(".error-message");
            const sentMessage = form.querySelector(".sent-message");
            const submitButton = form.querySelector('button[type="submit"]');

            // Reset previous messages
            if (errorMessage) {
                errorMessage.style.display = "none";
                errorMessage.textContent = "";
            }

            if (sentMessage) {
                sentMessage.style.display = "none";
                sentMessage.textContent = "";
            }

            if (loading) {
                loading.style.display = "block";
            }

            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: new FormData(form),
                    headers: {
                        "Accept": "application/json"
                    }
                });

                const result = await response.json();

                if (response.ok && result.status === "success") {
                    if (sentMessage) {
                        sentMessage.textContent =
                            result.message ||
                            "Your message has been sent successfully. Thank you!";

                        sentMessage.style.display = "block";
                    }

                    form.reset();
                } else {
                    if (errorMessage) {
                        errorMessage.textContent =
                            result.message ||
                            "Your message could not be sent. Please try again.";

                        errorMessage.style.display = "block";
                    }
                }
            } catch (error) {
                if (errorMessage) {
                    errorMessage.textContent =
                        "Unable to submit your message. Please try again.";
                    errorMessage.style.display = "block";
                }
            } finally {
                if (loading) {
                    loading.style.display = "none";
                }

                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    });
});