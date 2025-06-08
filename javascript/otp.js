document.addEventListener('DOMContentLoaded', function() {
    const otpBtn = document.getElementById('send-otp-btn');
    const emailInput = document.getElementById('email');
    const otpField = document.querySelector('.field.input');
    const otpInput = document.getElementById('otp');
    const countdownElement = document.getElementById('countdown');
    const errorText = document.querySelector('.error-text');
    
    let countdownInterval;
    let timeLeft = 300; // 5 minutes in seconds

otpBtn.addEventListener('click', async function() {
        const email = emailInput.value.trim();
        
        if (otpBtn.textContent === 'Send OTP' || otpBtn.textContent === 'Resend OTP') {
            // Send OTP logic
            if(email === '') {
                showError('Email is required to send OTP');
                return;
            }
            
            if(!validateEmail(email)) {
                showError('Please enter a valid email address');
                return;
            }
            
            // Set loading state
            otpBtn.disabled = true;
            otpBtn.textContent = 'Sending...';
            errorText.style.display = 'none';
            
            try {
                console.log('Attempting to send OTP to:', email); // Debug log
                
                const response = await fetch('php/send-otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}`
                });

                // First check if the response is OK
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status} ${response.statusText}`);
                }

                // Then try to parse JSON
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    throw new Error('Invalid server response format');
                }

                console.log('Server response:', data); // Debug log
                
                if (data.status === 'success') {
                    otpField.style.display = 'block';
                    otpBtn.textContent = 'Verify OTP';
                    startCountdown();
                    showSuccess('OTP sent to your email');
                    
                    // For debugging only - remove in production
                    console.log('OTP sent (dev only):', data.otp || 'Check your email');
                } else {
                    const errorMsg = data.message || 'Failed to send OTP';
                    showError(errorMsg);
                    otpBtn.textContent = 'Send OTP';
                    console.error('Server error:', errorMsg);
                }
            } catch (error) {
                const errorMsg = error.message.includes('Failed to fetch') 
                    ? 'Network connection failed. Check your internet.'
                    : `Error: ${error.message}`;
                
                showError(errorMsg);
                otpBtn.textContent = 'Send OTP';
                console.error('Full error:', error);
            } finally {
                otpBtn.disabled = false;
            }
        } else if (otpBtn.textContent === 'Verify OTP') {
            // Verify OTP logic
            const otp = otpInput.value.trim();
            
            if(otp === '' || otp.length !== 6) {
                showError('Please enter a valid 6-digit OTP');
                return;
            }
            
            otpBtn.disabled = true;
            otpBtn.textContent = 'Verifying...';
            errorText.style.display = 'none';
            
            try {
                console.log('Verifying OTP for email:', emailInput.value); // Debug log
                
                const response = await fetch('php/verify-otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(emailInput.value)}&otp=${encodeURIComponent(otp)}`
                });

                // Check response status first
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status} ${response.statusText}`);
                }

                // Parse JSON
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    throw new Error('Invalid verification response format');
                }

                console.log('Verification response:', data); // Debug log
                
                if(data.status === 'success') {
                    showSuccess('OTP verified successfully!');
                    otpBtn.textContent = 'Verified ✓';
                    otpBtn.style.backgroundColor = '#4CAF50';
                    otpBtn.disabled = true;
                    document.querySelector('form').dataset.otpVerified = 'true';
                } else {
                    const errorMsg = data.message || 'OTP verification failed';
                    showError(errorMsg);
                    otpBtn.textContent = 'Verify OTP';
                    console.error('Verification error:', errorMsg);
                }
            } catch (error) {
                const errorMsg = error.message.includes('Failed to fetch') 
                    ? 'Network error during verification'
                    : `Verification error: ${error.message}`;
                
                showError(errorMsg);
                otpBtn.textContent = 'Verify OTP';
                console.error('Full verification error:', error);
            } finally {
                otpBtn.disabled = false;
            }
        }
    });
    
    function startCountdown() {
        clearInterval(countdownInterval);
        timeLeft = 300;
        updateCountdown();
        
        countdownInterval = setInterval(function() {
            timeLeft--;
            updateCountdown();
            
            if(timeLeft <= 0) {
                clearInterval(countdownInterval);
                showError('OTP has expired. Please request a new one.');
                otpInput.disabled = true;
                otpBtn.textContent = 'Resend OTP';
            }
        }, 1000);
    }
    
    function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    function showError(message) {
        errorText.textContent = message;
        errorText.style.color = '#dc3545';
        errorText.style.display = 'block';
    }
    
    function showSuccess(message) {
        errorText.textContent = message;
        errorText.style.color = '#4CAF50';
        errorText.style.display = 'block';
        setTimeout(() => {
            errorText.style.display = 'none';
        }, 3000);
    }
});